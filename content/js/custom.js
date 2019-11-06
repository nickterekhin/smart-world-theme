$(document).ready(function ($) {

    $('.filter__chevron').click(function () {
        $('.filter').slideToggle();

        $('.filter__chevron svg').toggleClass('mod_rotate');
    });

    $('.term-description').click(function () {
        $(this).addClass('term_visible');
    })
})
