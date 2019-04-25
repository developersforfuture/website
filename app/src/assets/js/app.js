import $ from 'jquery'
$(document).ready(function() {
    // NAVBAR MOBILE COLLAPSE

    $(".navbar-burger").click(function() {
        $(".navbar-burger").toggleClass("is-active");
        $(".navbar-menu").toggleClass("is-active");
    });
    if (document.documentElement.scrollTop !== 0 || document.body.scrollTop !== 0) {
        $('nav').removeClass('not-scrolled');
    }
    $(window).scroll(() => {
        if (document.documentElement.scrollTop === 0 && document.body.scrollTop === 0) {
            $('nav').addClass('not-scrolled');
        } else {
            $('nav').removeClass('not-scrolled');
        }
    })
});
