require(['jquery', 'collapsibleBlock', 'vanilla-lazyLoad', 'mage/sticky', 'domReady!'], function ($, collapsibleList, LazyLoad) {

    var lazy = new LazyLoad({
        elements_selector: ".lazy"
    });

    $('.header.content .header.links').clone().appendTo('#store\\.links');

    $('.collapsible-block').collapsibleBlock();

    function toTop(ms = 2000){
        $('html, body').animate({
            scrollTop: 0
        }, ms);
    }

    toTop(0);

    $('#toTop').click(function () {
            toTop();
    });

    $(window).on('scroll', function () {
        var offset = $(document).scrollTop();

        if (offset > 500) {
            $('#toTop').addClass('fixed2');
        } else {
            $('#toTop').removeClass('fixed2');
        }
    });

    $('body').addClass('ready');

    var maxHeight = 0;

    $("div.product-item").each(function(){
        if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
    });

    $("div.product-item").height(maxHeight);



});