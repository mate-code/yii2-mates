$(document).ready(function () {

    function findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }

    $("li.sortable, table.sortable tbody").sortable({
        update: function () {
            var sort = findGetParameter("sort");
            if(sort !== null && sort !== "order") {
                $('body').alertBox({
                    type: 'warning',
                    message: "Order should not be changed when the list is sorted.<br>Sort by order to be able so set the order."
                });
                return;
            }

            var orderData = {};
            var position = 1;
            $(this).children(".ui-sortable-handle").each(function () {
                var id = $(this).data("key");
                orderData[position] = {
                    id: id,
                    order: position
                };
                $(this).find(".order-content").html(position);
                position++;
            });

            var updateUrl = $(this).data("sortable-update");
            if(!updateUrl) {
                updateUrl = $(this).parents("[data-sortable-update]").data("sortable-update");
            }
            if(!updateUrl) {
                $('body').alertBox({
                    type: 'danger',
                    message: "Failed to find update URL."
                });
                return;
            }

            $.post(updateUrl,
                {
                    order: orderData
                },
                function (data) {
                    if(data["status"] === "success") {
                        $('body').alertBox({
                            type: 'success',
                            message: "Order has successfully been updated"
                        });
                    } else {
                        $('body').alertBox({
                            type: 'danger',
                            message: "Error updating the order"
                        });
                    }
                },
                "json"
            ).fail(function () {
                $('body').alertBox({
                    type: 'danger',
                    message: "Error updating the order"
                });
            });
        }
    });

    // fix the width of columns to not make them resize when they are dragged

    $("tbody.ui-sortable td").each(function () {
        $(this).css("width", $(this).outerWidth());
    })

});