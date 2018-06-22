
$(document).ready(function () {

    $('.unite-gallery').each(function () {
        var options = $(this).data('options');
        options = options ? options : {};
        $(this).unitegallery($.extend({}, {
            lightbox_show_numbers: false
        }, options));
    });

});