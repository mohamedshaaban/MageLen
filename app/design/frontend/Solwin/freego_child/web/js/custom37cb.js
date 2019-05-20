jQuery(window).load(function() {
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
        theme: 'light_rounded',
        social_tools: false,
        deeplinking: false,
    });
});
jQuery(document).ready(function() {
    var ww = jQuery(window).width();

    if (ww > 479 && ww < 719) {
        jQuery('.client').removeClass('last');
    }

    jQuery("area[rel^='prettyPhoto']").prettyPhoto();
    jQuery(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({ animation_speed: 'normal', theme: 'light_square', slideshow: 3000, autoplay_slideshow: false });
    jQuery(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({ animation_speed: 'fast', slideshow: 10000, hideflash: true });
    jQuery("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
        custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
        changepicturecallback: function() { initialize(); }
    });
    jQuery("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
        custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
        changepicturecallback: function() { _bsap.exec(); }
    });

});

// NAVIGATION CALLBACK
var ww = jQuery(window).width();
jQuery(document).ready(function() {
    jQuery(".nav li a").each(function() {
        if (jQuery(this).next().length > 0) {
            jQuery(this).addClass("parent");
        };
    })
    jQuery(".toggleMenu").click(function(e) {
        e.preventDefault();
        jQuery(this).toggleClass("active");
        jQuery(".nav").slideToggle('fast');
    });
    adjustMenu();
})

// navigation orientation resize callbak
jQuery(window).bind('resize orientationchange', function() {
    ww = jQuery(window).width();
    adjustMenu();
});

var adjustMenu = function() {
    if (ww < 981) {
        jQuery(".toggleMenu").css("display", "block");
        if (!jQuery(".toggleMenu").hasClass("active")) {
            jQuery(".nav").hide();
        } else {
            jQuery(".nav").show();
        }
        jQuery(".nav li").unbind('mouseenter mouseleave');
    } else {
        jQuery(".toggleMenu").css("display", "none");
        jQuery(".nav").show();
        jQuery(".nav li").removeClass("hover");
        jQuery(".nav li a").unbind('click');
        jQuery(".nav li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
            jQuery(this).toggleClass('hover');
        });
    }
}
jQuery(document).ready(function() {
    jQuery('.srchicon').click(function() {
        jQuery('.searchtop').toggle();
        jQuery('.topsocial').toggle();
    });
});

jQuery(document).ready(function() {
    var submitIcon = jQuery('.searchbox-icon');
    var inputBox = jQuery('.searchbox-input');
    var searchBox = jQuery('.searchbox');
    var isOpen = false;
    submitIcon.click(function() {
        if (isOpen == false) {
            searchBox.addClass('searchbox-open');
            inputBox.focus();
            isOpen = true;
        } else {
            searchBox.removeClass('searchbox-open');
            inputBox.focusout();
            isOpen = false;
        }
    });
    submitIcon.mouseup(function() {
        return false;
    });
    searchBox.mouseup(function() {
        return false;
    });
    jQuery(document).mouseup(function() {
        if (isOpen == true) {
            jQuery('.searchbox-icon').css('display', 'block');
            submitIcon.click();
        }
    });
});

function buttonUp() {
    var inputVal = jQuery('.searchbox-input').val();
    inputVal = jQuery.trim(inputVal).length;
    if (inputVal !== 0) {
        jQuery('.searchbox-icon').css('display', 'none');
    } else {
        jQuery('.searchbox-input').val('');
        jQuery('.searchbox-icon').css('display', 'block');
    }
}

jQuery(document).ready(function($) {
    $('.header .header-inner .logo h1, h2.section_title').html(function() {
        var text = $(this).text().split(' ');
        var last = text.pop();
        return text.join(" ") + (text.length > 0 ? ' <span>' + last + '</span>' : last);
    });
});

jQuery.noConflict();
jQuery(window).load(function() {
    jQuery('.testimonials').owlCarousel({
        loop: false,
        rtl: true,
        autoplay: false,
        autoHeight: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        margin: 0,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        dots: false,
        responsiveClass: true,
        responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 1 }
        }
    })
});

jQuery(document).ready(function() {
    jQuery('.view-product-carousel').owlCarousel({
        loop: true,
        autoplay: false,
        autoHeight: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        margin: 0,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        dots: false,
        responsiveClass: true,
        responsive: {
            0: { items: 1 },
            480: { items: 2 },
            600: { items: 3 },
            1000: { items: 4 }
        }
    })
});

jQuery(document).ready(function() {
    alert('sss');
    jQuery('.logoslider').owlCarousel({
        rtl: true,
        loop: true,
        autoplay: false,
        autoHeight: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        margin: 40,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        dots: false,
        responsiveClass: true,
        responsive: {
            0: { items: 1 },
            480: { items: 2 },
            600: { items: 3 },
            1000: { items: 5 }
        }
    })
});