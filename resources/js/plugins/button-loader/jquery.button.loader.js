/*A jQuery plugin which add loading indicators into buttons
* By Minoli Perera
* MIT Licensed.
*/
(function ($) {
    $('.has-spinner').attr("disabled", false);
    $.fn.buttonLoader = function (action) {
        var self = $(this);
        if (action == 'start') {
            if ($(self).attr("disabled") == "disabled") {
                return false;
            }
            $('.has-spinner').attr("disabled", true);
            $(self).attr('data-btn-text', $(self).text());
            var text = 'Loading';
            if($(self).attr('data-load-text') != undefined && $(self).attr('data-load-text') != ""){
                console.log($(self).attr('data-load-text'));
                var text = $(self).attr('data-load-text');
            }
            $(self).html('<span class="spinner"><i class="ti ti-loader" title="button-loader"></i></span> '+text);
            $(self).addClass('active');
        }
        if (action == 'stop') {
            $(self).html($(self).attr('data-btn-text'));
            $(self).removeClass('active');
            $('.has-spinner').attr("disabled", false);
        }
    }
})(jQuery);
