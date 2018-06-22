/**
 * Created by Marius on 14.08.2017.
 */

function setLoadingIconToOverlay(e) {
    $(e.currentTarget).find(".template-upload .progress[aria-valuenow='100']")
        .parent().parent()
        .find(".upload-overlay")
        .html('<div class="loading"><div></div></div>');
}

function validateFileUpload(e, data) {

    if(typeof data.originalFiles[0] === "undefined") {
        console.error("File verification failed: No file data given");
        return null;
    }
    if(typeof data.originalFiles[0].size === "undefined") {
        console.error("File verification failed: No size given");
        return null;
    }
    if(typeof data.originalFiles[0].type === "undefined") {
        console.error("File verification failed: No file type given");
        return null;
    }

    var options = typeof $.fn.fileupload.options !== 'undefined' ? $.fn.fileupload.options : {};
    var defaultOptions = {
        acceptFileTypes: /^image\/(gif|jpe?g|png)$/i,
        maxFileSize: 2000000
    };
    options = $.extend(true, {}, defaultOptions, options);

    $.each(data.originalFiles, function (i, file) {
        if(typeof file.preview === "undefined" || typeof file.isValidated !== "undefined") {
            return;
        }

        var uploadErrors = [];

        if(!options.acceptFileTypes.test(file.type)) {
            uploadErrors.push('Not an accepted file type');
        }

        if(options.maxFileSize && file.size > options.maxFileSize) {
            if(typeof options.maxFileSizeLabel === "undefined") {
                options.maxFileSizeLabel = (Math.round(options.maxFileSize / 10000) / 100) + " MB";
            }
            uploadErrors.push('File is bigger than ' + options.maxFileSizeLabel);
        }

        if(uploadErrors.length > 0) {
            var fileItem = $(file.preview.offsetParent);
            var overlay = fileItem.find(".upload-overlay");
            var uploadButton = overlay.find(".glyphicon");
            fileItem.addClass("has-error");
            overlay.removeClass("start");
            uploadButton.removeClass("glyphicon-upload").addClass("glyphicon-ban-circle");

            var errorContainer = fileItem.find(".error");
            $.each(uploadErrors, function (i, errorMsg) {
                errorContainer.append('<div class="error-msg">' + errorMsg + '</div>');
            });
        }
        file.isValidated = true;
    });

}