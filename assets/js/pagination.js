function paginate(totalRows, current_page) {
    $('#hdn_current_page').val(current_page);
    var rowsPerPage = 8;
    var pagesToShow = 5;
    var totalPages = Math.ceil(totalRows / rowsPerPage);
    
    if (totalPages <= 1) {
        return "";
    }

    var currentGroup = Math.ceil(current_page / pagesToShow);
    var startPage = (currentGroup - 1) * pagesToShow + 1;
    var endPage = Math.min(startPage + pagesToShow - 1, totalPages);

    var html = "<div class='pagination-container'>";
    
    // Previous button
    if (current_page > 1) {
        var prevPage = current_page - 1;
        html += "<button type='button' onclick='loadData(" + prevPage + ")' class='pagination-pill' title='Previous Page'><i class='fa fa-chevron-left'></i></button>";
    }

    // Numbered pills
    for (var i = startPage; i <= endPage; i++) {
        var activeClass = (i == current_page) ? "active" : "";
        html += "<button type='button' onclick='loadData(" + i + ")' class='pagination-pill " + activeClass + "'>" + i + "</button>";
    }

    // Next button
    if (current_page < totalPages) {
        var nextPage = current_page + 1;
        html += "<button type='button' onclick='loadData(" + nextPage + ")' class='pagination-pill' title='Next Page'><i class='fa fa-chevron-right'></i></button>";
    }

    html += "</div>";
    return html;
}

function updatePagination(currentPage) {
    $('.pagination-pill').removeClass('active');
    $('.pagination-pill').each(function() {
        if ($(this).text() == currentPage) {
            $(this).addClass('active');
        }
    });
}
