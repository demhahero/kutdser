(function (jQuery) {
    "use strict";
    // BG Color & Image
    jQuery('section,div,a,h1,h2,h3,h4,h5,h6,p,span,footer').each(function(){
        var bg_color = jQuery(this).attr("data-color");
        if(bg_color){
            jQuery(this).css("background-color", "" + bg_color + "");
        }
        var txt_color = jQuery(this).attr("data-txt-color");
        if(txt_color){
            jQuery(this).css("color", "" + txt_color + "");
        }
        var l_height = jQuery(this).attr("data-line-height");
        if(l_height){
            jQuery(this).css("line-height", "" + l_height + "");
        }
        var url = jQuery(this).attr("data-image");
        if(url){
            jQuery(this).css("background-image", "url(" + url + ")");
        }
    });
    // Elements Offset
    function _bannerOffset(){
        if(jQuery('._banner2').length > 0){
            var off_set = jQuery('.container').offset().left;
            jQuery('._banner ._holder').css('margin-left',(off_set+15)+'px');
        }
    } _bannerOffset();
    var resizeHandler = function(){
        _bannerOffset();
    };
    jQuery(window).resize(resizeHandler);
    setTimeout(function() {
        _bannerOffset();
    }, 2000);
    // Scroll To Bottom
    jQuery('._scrollTo').on('click',function(){
        var n_target = jQuery(this).parent().next();
        jQuery('html,body').animate({
                scrollTop: jQuery(n_target).offset().top-60},
            'slow');
    });
    // Youtube Video
    jQuery('._inspiration ._img span').on('click', function(ev) {
        ev.preventDefault();
        jQuery(this).hide();
        var vi = jQuery("#yVideo");
        vi.css('opacity','1');
        vi[0].src += "?rel=0&autoplay=1";
    });
    // Top Scroll
    jQuery("._top").on('click',function(e) {
        e.preventDefault();
        jQuery("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });
    // Table Scroller
    jQuery('._table ul li ._h').on('click',function(){
        var ths = jQuery(this);
        if(ths.hasClass('active')){
            ths.removeClass('active');
        } else {
            jQuery('._table ul li ._h').removeClass('active');
            ths.addClass('active');
        }
    });
    // Tabs
    jQuery('._tabHead li').on('click',function(e){
        e.preventDefault();
        var ths = jQuery(this);
        var _id = jQuery(this).attr('data-id');
        jQuery('._tabHead li').removeClass('active');
        ths.addClass('active');
        jQuery('._tabs ._tab').hide();
        jQuery(_id).show();
    });
    // Toggle
    jQuery('._toggle h3').on('click',function(){
        var ths = jQuery(this);
        if(ths.hasClass('active')){
            ths.removeClass('active');
        } else {
            jQuery('._toggle h3').removeClass('active');
            ths.addClass('active');
        }
    });
    jQuery('._cBanner ol._thumbs li').on('click',function(){
        var ths = jQuery(this);
        jQuery('._cBanner ol._thumbs li').removeClass('active');
        ths.addClass('active');
    });
    // Collapse
    jQuery('.navbar-toggle,.navbar-collapse').on('hidden.bs.collapse', function () {
        jQuery('.navbar-static-top').removeClass('topBlack');
    });
    jQuery('.navbar-toggle,.navbar-collapse').on('show.bs.collapse', function () {
        jQuery('.navbar-static-top').addClass('topBlack');
    });
    // Sub Menu's
    jQuery('.menu-item-has-children > a').each(function(){
        jQuery(this).addClass('dropdown-toggle');
        jQuery(this).attr('data-toggle','dropdown');
    });
    jQuery('.menu-item-has-children').each(function(){
        jQuery(this).addClass('dropdown');
    });
})(jQuery);