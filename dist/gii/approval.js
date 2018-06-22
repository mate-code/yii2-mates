
if(typeof afterApprovalLoad === "undefined") {
    var afterApprovalLoad = [];
}
$(document).ready(function () {

    var approvalViews = $("#approval-views");
    var currentPos = 0;
    var approveCountElement = $(".approve-count");

    function findViewElement(pos) {
        var viewElement = approvalViews.find("[data-pos='" + pos + "']");
        if (typeof viewElement.data("view-url") === "undefined") {
            return false;
        } else {
            return viewElement;
        }
    }
    function findNext() {
        return findViewElement(currentPos + 1);
    }
    function findPrev() {
        return findViewElement(currentPos - 1);
    }

    function disableButtons() {
        if (findPrev() === false) {
            $(".approve-previous").addClass("disabled");
        } else {
            $(".approve-previous").removeClass("disabled");
        }
        if (findNext() === false) {
            $(".approve-next").addClass("disabled");
        } else {
            $(".approve-next").removeClass("disabled");
        }
    }

    function setupApprovalForm(viewElement) {
        viewElement.find("form button[type=submit].approval-btn").click(function() {
            var approved = $(this).attr("value");
            viewElement.find("input#value-approved").attr("value", approved);
        });

        viewElement.find(".ajax-form").submit(function(e) {
            e.preventDefault();

            var url = $(this).attr("action");
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function(data)
                {
                    if(window.JSON.parse(data).status === "success") {
                        var count = parseInt(approveCountElement.html());
                        approveCountElement.html(count - 1);

                        viewElement.remove();
                        if($.trim(approvalViews.html()) !== "") {
                            var loadElement = findNext();
                            if(!loadElement) loadElement = findPrev();
                            if(!loadElement) loadElement = approvalViews.find(":first-child");

                            var approved = viewElement.find("input#value-approved").attr("value");
                            if(approved === "Yes") {
                                alertBox("Entry has been approved");
                            } else if(approved === "No") {
                                alertBox("Entry has been declined");
                            }

                            loadApprovalContent(loadElement);
                        } else {
                            window.location.href = $("#return-url").data("url");
                        }
                    } else {
                        alertBox("Error approving the entry", "danger");
                    }
                }
            });
        });
    }

    afterApprovalLoad.push(setupAnimatedFormLabels);
    afterApprovalLoad.push(setupApprovalForm);
    afterApprovalLoad.push(disableButtons);

    function loadApprovalContent(viewElement, reload) {
        reload = typeof reload === "undefined" ? false : reload;
        if($.trim(viewElement.html()) !== "" && reload === false) {
            approvalViews.find(".approval-view").hide();
            disableButtons();
            viewElement.show();
            return null;
        }

        var viewUrl = viewElement.data("view-url");
        $.get(viewUrl).done(function (content) {
            approvalViews.find(".approval-view").hide();
            viewElement.html(content);
            $.each(afterApprovalLoad, function (i, eventFunction) {
                eventFunction(viewElement);
            });
            viewElement.show();
        })
    }

    var firstElement = findViewElement(currentPos);
    if(firstElement) {
        loadApprovalContent(findViewElement(currentPos));
    }

    $(".approve-previous").click(function () {
        var viewElement = findPrev();
        if (viewElement) {
            currentPos--;
            loadApprovalContent(viewElement);
        }
    });

    $(".approve-next").click(function () {
        var viewElement = findNext();
        if (viewElement) {
            currentPos++;
            loadApprovalContent(viewElement);
        }
    });

});