function applyActiveMenu() {
    var currentPage = window.location.pathname.split("/").pop();
    if (!currentPage || currentPage === "") {
        currentPage = "dashboard.php";
    }

    $('#side-menu li').removeClass('active');
    $('#side-menu a').each(function() {
        var href = $(this).attr('href');
        if (href) {
            var cleanHref = href.split('?')[0].split('#')[0];
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
    var cachedMenu = sessionStorage.getItem('finez_seo_menu_v13');
    if (cachedMenu && cachedMenu.indexOf('dashboard.php') !== -1) {
        $('#divMenuContainer').html(cachedMenu);
        applyActiveMenu();
    }

    // 2. Background sync to ensure menu is always fresh
    $.ajax({
        type: "POST",
        url: "../includes/menu.php",
        data: {
            action: 'load_menu_data',
        },
        success: function(data) {
            if (data && data !== cachedMenu) {
                sessionStorage.setItem('finez_seo_menu_v13', data);
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
$(document).on('click', 'a[href*="logout.php"]', function() {
    sessionStorage.removeItem('finez_seo_menu');
    sessionStorage.removeItem('finez_seo_menu_v3');
    sessionStorage.removeItem('finez_seo_menu_v7');
    sessionStorage.removeItem('finez_seo_menu_v8');
    sessionStorage.removeItem('finez_seo_menu_v9');
    sessionStorage.removeItem('finez_seo_menu_v10');
    sessionStorage.removeItem('finez_seo_menu_v11');
    sessionStorage.removeItem('finez_seo_menu_v13');
});
