function applyActiveMenu() {
    var currentPage = window.location.pathname.split("/").pop();
    currentPage = currentPage ? currentPage.replace(/\.php$/, '') : '';
    if (!currentPage || currentPage === "") {
        currentPage = "dashboard";
    }

    $('#side-menu li').removeClass('active');
    $('#side-menu a').each(function() {
        var href = $(this).attr('href');
        if (href) {
            var cleanHref = href.split('?')[0].split('#')[0].replace(/\.php$/, '');
            if (cleanHref === currentPage) {
                $(this).closest('li').addClass('active');
            }
        }
    });

    try {
        $('#side-menu').metisMenu('dispose');
    } catch (e) {}
    try {
        $('#side-menu').metisMenu();
    } catch (e) {}

    // Ensure menu bar is NOT scrollable
    try {
        $('.sidebar-collapse').slimScroll({ destroy: true });
        $('.sidebar-collapse').removeAttr('style');
        localStorage.removeItem('fixedsidebar');
    } catch (e) {}
}

function loadMenu() {
    // 1. Instant rendering from session cache if available and up-to-date
    var cachedMenu = sessionStorage.getItem('finez_seo_menu_v14');
    if (cachedMenu && cachedMenu.indexOf('dashboard') !== -1) {
        $('#divMenuContainer').html(cachedMenu);
        applyActiveMenu();
    }

    // 2. Background sync to ensure menu is always fresh
    $.ajax({
        type: "POST",
        url: "../includes/menu",
        data: {
            action: 'load_menu_data',
        },
        success: function(data) {
            if (data && data !== cachedMenu) {
                sessionStorage.setItem('finez_seo_menu_v14', data);
                $('#divMenuContainer').html(data);
                applyActiveMenu();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX menu error: ', status, error);
        }
    });
}

// Clear cached menu on logout
$(document).on('click', 'a[href*="logout"]', function() {
    sessionStorage.clear();
});
