$(function() {
    $('a[href="'+document.location.pathname+'"]').addClass('is-active');

    $('.notification .delete').on('click', function() {
        $(this).parent().fadeOut(400, function() {
            $(this).remove();
        });
    });

    var $toggle = $('.header-toggle');
    var $menu = $('.header-menu');

    $toggle.click(function() {
        $(this).toggleClass('is-active');
        $menu.toggleClass('is-active');
    });
});