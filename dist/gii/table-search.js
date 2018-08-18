$.fn.tableSearch = function () {

    var form = this;
    this.targetTable = $(form.data('target-table'));
    if (this.targetTable.length < 1) {
        console.error("TableSearch: Unable to find target table");
        return null;
    }
    this.targetTableBody = this.targetTable.find('tbody');
    if (this.targetTableBody.length < 1) {
        console.error("TableSearch: Target table does not have a tbody");
        return null;
    }

    this.formFields = this.targetTable.find('thead').find('input,select,textarea');

    this.formInputs = this.targetTable.find('thead').find('input,textarea');
    this.formSelects = this.targetTable.find('thead').find('select');
    this.formDatePicker = this.targetTable.find('thead').find('.date-picker input');
    this.pagination = null;
    this.sendTimeOut = null;

    this.formIsEmpty = function () {
        var isEmpty = true;
        $.each(this.formFields.serializeArray(), function (i, data) {
            if(data.value === "" || -1 !== $.inArray(data.name, ['page', 'per-page'])) {
                return null;
            }
            console.log(data);
            isEmpty = false;
            return false;
        });
        return isEmpty;
    };

    this.sendSearchForm = function () {
        var actionUrl = form.attr("action");
        var formData = this.formFields.serialize();
        $.ajax({
            method: "get",
            url: actionUrl,
            data: formData,
            dataType: "html"
        }).done(function (data) {
            form.targetTableBody.remove();
            form.targetTable.append($(data));
            form.targetTableBody = form.targetTable.find('tbody');
            form.createPagination();
            form.targetTable.trigger('update');
        });
    };

    this.createPagination = function () {
        if (form.pagination) form.pagination.remove();
        var pageCount = parseInt(form.targetTableBody.data('page-count'));
        if (pageCount < 2) {
            return null;
        }
        var pageSize = parseInt(form.targetTableBody.data('page-size'));
        var currentPage = parseInt(form.targetTableBody.data('page'));

        form.pagination = $('<ul class="pagination"></ul>');
        form.pagination.pageCount = pageCount;
        form.pagination.pageSize = pageSize;
        form.pagination.currentPage = currentPage;

        var firstLink = 1;
        var lastLink = pageCount;
        var maxLinks = 16;
        if(pageCount > maxLinks) {
            lastLink = maxLinks;
            if(currentPage > (maxLinks/2)) {
                firstLink = currentPage - (maxLinks/2);
                lastLink = firstLink + maxLinks;
            }
            if(lastLink > pageCount) {
                firstLink = pageCount - maxLinks;
                lastLink = pageCount;
            }
        }

        if(firstLink != 1) {
            var first = $('<li></li>');
            first.html('<a href="#" data-page="1">««</a>');
            form.pagination.append(first);
            form.pagination.firstButton = first;
        }

        var prev = $('<li class="prev"></li>');
        if (currentPage === 1) prev.addClass('disabled');
        prev.html('<a href="#" data-page="' + (currentPage - 1) + '">«</a>');
        form.pagination.append(prev);
        form.pagination.prevButton = prev;

        if(firstLink != 1) {
            form.pagination.append($('<li class="disabled"><a href="#">...</a></li>'));
        }

        form.pagination.pageButtons = [];
        for (var i = firstLink; i <= lastLink; i++) {
            var page = $('<li></li>');
            if (currentPage === i) page.addClass('active');
            page.html('<a href="#" data-page="' + i + '">' + i + '</a>');
            form.pagination.append(page);
            form.pagination.pageButtons[i] = page;
        }

        if(lastLink != pageCount) {
            form.pagination.append($('<li class="disabled"><a href="#">...</a></li>'));
        }

        var next = $('<li class="prev"></li>');
        if (currentPage === pageCount) next.addClass('disabled');
        next.html('<a href="#" data-page="' + (currentPage + 1) + '">»</a>');
        form.pagination.append(next);
        form.pagination.nextButton = next;

        if(lastLink != pageCount) {
            var last = $('<li></li>');
            last.html('<a href="#" data-page="' + pageCount + '">»»</a>');
            form.pagination.append(last);
            form.pagination.lastButton = last;
        }

        form.pagination.find('li:not(.disabled) a').click(function (e) {
            e.preventDefault();
            form.switchPage($(this).data('page'));
        });

        form.targetTable.after(form.pagination);
        return form.pagination;
    };

    this.switchPage = function (page) {
        if (!form.pagination || typeof page === 'undefined') {
            return null;
        }
        var pageInput = this.targetTable.find('thead').find('[name=page]');
        if (!pageInput) {
            console.error("No input for page number (name=page) found within <thead>");
            return null;
        }
        var pageSizeInput = this.targetTable.find('thead').find('[name="per-page"]');
        if (!pageSizeInput) {
            console.error("No input for page size (name=per-page) found within <thead>");
            return null;
        }

        pageInput.val(page);
        pageSizeInput.val(form.pagination.pageSize);
        form.sendSearchForm();

        if (page === 1) {
            form.pagination.prevButton.addClass('disabled');
        } else {
            form.pagination.prevButton.removeClass('disabled');
            form.pagination.prevButton.find('a').data('page', page - 1);
        }

        if (page === form.pagination.pageCount) {
            form.pagination.nextButton.addClass('disabled');
        } else {
            form.pagination.nextButton.removeClass('disabled');
            form.pagination.nextButton.find('a').data('page', page + 1);
        }
    };

    this.getPagination = function () {
        if (!form.pagination) {
            form.pagination = form.createPagination();
        }
        return form.pagination;
    };

    this.createPagination();

    this.formInputs.on("keyup", function () {
        clearTimeout(form.sendTimeOut);
        form.sendTimeOut = setTimeout(function () {
            form.sendSearchForm();
        }, 400);
    });
    this.formSelects.on("change", function () {
        form.sendSearchForm();
    });
    this.formDatePicker.on("changeDate", function () {
        form.sendSearchForm();
    });

    if(!this.formIsEmpty()) {
        this.sendSearchForm();
    }

    $(".search-clear").click(function () {
        form.formInputs.val("");
        form.formSelects.select2("val", "");
    });

    return form;
};

$(document).ready(function () {

    // searching results
    $('.table-search-form').each(function () {
        $(this).tableSearch();
    });

});