//Logo Typing Effect Create By MSI:313
msiTyping("#logo_typing", 350, 0);

//Login Register Form
var x = document.getElementById('login')
var y = document.getElementById('register')
var z = document.getElementById('btn')
var m = document.getElementById('mobile')

function login() {
    x.style.left = "30px";
    y.style.left = "430px";
    z.style.left = "0px";
    m.style.height = "450px";
}

function register() {
    x.style.left = "-370px";
    y.style.left = "30px";
    z.style.left = "110px";
    m.style.height = "450px";
}

//Responsive Auto Scroll Table
var $el = $(".table__responsive");

function anim() {
    var st = $el.scrollLeft();
    var sb = $el.prop("scrollWidth") - $el.innerWidth();
    $el.animate({
        scrollLeft: st < sb / 2 ? sb : 0
    }, 8000, anim);
}

function stop() {
    $el.stop();
}
anim();
$el.hover(stop, anim);


$(function () {

    $(".toggle-password").click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(".toggle-password-r").click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });


    //Student Counter
    $('.counter').counterUp({
        delay: 19,
        time: 1919
    });

    //add_slider Slider
    $('.add_slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        infinite: true,
        speed: 1000,
        autoplaySpeed: 4000,
        arrows: false,
        responsive: [{
                breakpoint: 1121,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }
        ]
    });

    //Testimonial Slider
    $('.testimonial_slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        infinite: true,
        speed: 1000,
        autoplaySpeed: 4000,
        arrows: false,
        responsive: [{
                breakpoint: 1121,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }
        ]
    });

});
