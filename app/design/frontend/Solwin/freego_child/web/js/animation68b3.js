
// .bounce, .flash, .pulse, .shake, .swing, .tada, .wobble, .bounceIn, .bounceInDown, .bounceInLeft, .bounceInRight, .bounceInUp, .bounceOut, .bounceOutDown, .bounceOutLeft, .bounceOutRight, .bounceOutUp, .fadeIn, .fadeInDown, .fadeInDownBig, .fadeInLeft, .fadeInLeftBig, .fadeInRight, .fadeInRightBig, .fadeInUp, .fadeInUpBig, .fadeOut, .fadeOutDown, .fadeOutDownBig, .fadeOutLeft, .fadeOutLeftBig, .fadeOutRight, .fadeOutRightBig, .fadeOutUp, .fadeOutUpBig, .flip, .flipInX, .flipInY, .flipOutX, .flipOutY, .lightSpeedIn, .lightSpeedOut, .rotateIn, .rotateInDownLeft, .rotateInDownRight, .rotateInUpLeft, .rotateInUpRight, .rotateOut, .rotateOutDownLeft, .rotateOutDownRight, .rotateOutUpLeft, .rotateOutUpRight, .slideInDown, .slideInLeft, .slideInRight, .slideOutLeft, .slideOutRight, .slideOutUp, .rollIn, .rollOut, .zoomIn, .zoomInDown, .zoomInLeft, .zoomInRight, .zoomInUp
require(['jquery'],function($){
    // $.noConflict();
    
    
    
    
    
    $(window).load(function() {
        $(window).scroll(function() {
            // This is then function used to detect if the element is scrolled into view
            function elementScrolled(elem) {
                console.log(elem);
                if(!$(elem).length )         // use this if you are using id to check
                {
                    return;
                }
                var docViewTop = jQuery(window).scrollTop();
                var docViewBottom = docViewTop + jQuery(window).height();
                var elemTop = jQuery(elem).offset().top;
                return ((elemTop <= docViewBottom) && (elemTop >= docViewTop));
            }
    
            // This is where we use the function to detect if ".box2" is scrolled into view, and when it is add the class ".animated" to the <p> child element
            if (elementScrolled('.container .threebox')) {
                var els = jQuery('.container .threebox'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInDown');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
            if (elementScrolled('#section2 .container')) {
                var els = jQuery('#section2 .container'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInRight');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
    
            if (elementScrolled('#section3 .container')) {
                var els = jQuery('#section3 .container'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInUp');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
    
            // if(elementScrolled('#section4 .container')) {
            //     var els = jQuery('#section4 .container'),
            //         i = 0,
            //         f = function () {
            //             jQuery(els[i++]).addClass('fadeInLeft');
            //             if(i < els.length) setTimeout(f, 400);
            //         };
            //     f();
            // }
    
            // if(elementScrolled('#section5 .container')) {
            //     var els = jQuery('#section5 .container'),
            //         i = 0,
            //         f = function () {
            //             jQuery(els[i++]).addClass('fadeInUp');
            //             if(i < els.length) setTimeout(f, 400);
            //         };
            //     f();
            // }
    
            if (elementScrolled('#section6 .container')) {
                var els = jQuery('#section6 .container'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInRight');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
    
            if (elementScrolled('#section7 .container')) {
                var els = jQuery('#section7 .container'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInUp');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
    
            if (elementScrolled('#section8 .news-box')) {
                var els = jQuery('#section8 .news-box'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInRight');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
            if (elementScrolled('#footer-wrapper .container')) {
                var els = jQuery('#footer-wrapper .container'),
                    i = 0,
                    f = function() {
                        jQuery(els[i++]).addClass('fadeInUp');
                        if (i < els.length) setTimeout(f, 400);
                    };
                f();
            }
    
        });
        $(document).ready(function(){
    
            // right lens
            $("#fieldNext1").click(function() {
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                }); 
          
                var str = "";
              
        
                $(select_id[1] + ' option:selected').next().attr('selected', 'selected');
                $(select_id[1] + ' option[selected = selected]').prev().removeAttr('selected');
                $(select_id[1] + " option:selected").each(function() {
                    str = $(this).text() + " ";
                    
                });
                $("#pow-R").text(str);
                $(select_id[1]).trigger('change');
        
            });
        
            $("#fieldPrev1").click(function() {
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
        
                var str = "";
             
                $(select_id[1] + ' option:selected').prev().attr('selected', 'selected');
                $(select_id[1] + ' option[selected = selected]').next().removeAttr('selected');
                $(select_id[1] + " option:selected").each(function() {
                    str = $(this).text() + " ";
                });
                $("#pow-R").text(str);
                $(select_id[1]).trigger('change');
    
            });
        
        
            // left lens 
            $("#fieldNext").click(function() {
             
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
                var str1 = "";
               
        
                $(select_id[0] +' option:selected').next().attr('selected', 'selected');
                $(select_id[0] + ' option[selected = selected]').prev().removeAttr('selected');
                $(select_id[0] + " option:selected").each(function() {
                    str1 = $(this).text() + " ";
                });
                $("#pow-L").text(str1);
                $(select_id[0]).trigger('change');
            });
        
            $("#fieldPrev").click(function() {
                
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
                var str = "";
            
        
                
                $(select_id[0] +' option:selected').prev().attr('selected', 'selected');
                $(select_id[0] +' option[selected = selected]').next().removeAttr('selected');
                $(select_id[0] + " option:selected").each(function() {
                    str = $(this).text() + " ";
                });
                $("#pow-L").text(str);
                $(select_id[0]).trigger('change');            
            });
            
            // for localstorage saved option
                var slected_text = [] ;
                var quantity_id = [];
                var select_id = [];
             $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
              
                var option1 = localStorage.getItem('option1');
                var option2 = localStorage.getItem('option2');
                if(option1 != ""){
                    $(select_id[0] + " option[value=" + option1 +"]").attr("selected","selected") ;
                }
                if(option2 != ""){
                     $(select_id[1] + " option[value=" + option2 +"]").attr("selected","selected") ;
                }
        
                // for eye-circle class
                $(select_id[1] + " option:selected").each(function() {
                    str1 = $(this).text() + " ";
                });
                $(select_id[0] + " option:selected").each(function() {
                    str2 = $(this).text() + " ";
                });
                $("#pow-R").text(str1);
                $("#pow-L").text(str2);
                // end eye-circle class
          
               // end localstorage saved option
            $(".fieldset-bundle-options .qty-holder").hide();        
            $('.fieldset-bundle-options select').change(function(){
                var slected_text = [] ;
                var quantity_id = [];
                var select_id = [];
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
                
                    if(slected_text[0] == slected_text[1] ){
                         $(select_id[1] + ' option:selected').removeAttr('selected');
                         $(select_id[1] + ' option:selected').html(slected_text[0]);
                         $(select_id[1]).trigger('change');
                    }
            });
        
            $('.fieldset-bundle-options select').focus(function(){
                
              
            //    alert( $('#' + $(this).attr('id') + ' option:first').html());
            if($('#' + $(this).attr('id') + ' option:first').html() != 'Choose a selection...' ){
                $('#' + $(this).attr('id') + ' option:first').html('Choose a selection...');
            }
                
            });
        
            $('#save_prescription').click(function(){
                var slected_text = [] ;
                var quantity_id = [];
                var select_id = [];
                $('#save_prescription').html('saving ...');
                $('.fieldset-bundle-options select').each(function(){
                    slected_text.push($(this).find('option:selected').text()) ;
                    // alert($(this).find('option:selected').text());
                    quantity_id.push("#" + $(this).attr('id') + '-qty-input');
                    select_id.push('#' + $(this).attr('id'));
                    // alert($('#' + id + '-qty-input').val());
                });
                    if(slected_text[0] == slected_text[1] ){
                         $(select_id[1] + ' option:selected').removeAttr('selected');
                         $(select_id[1] + ' option:selected').html(slected_text[0]);
                    }
                    localStorage.setItem('option1', $(select_id[0]).val());
                    localStorage.setItem('option2', $(select_id[1]).val());
        
                    setTimeout(function(){ 
                        $('#save_prescription').html('Save Prescription');
                     }, 1500);
                    // document.cookie =  select_id[1] + "=" + $(select_id[1]).val();
                    // document.cookie =  select_id[0] + "=" + $(select_id[0]).val();
               
            });  
        
        });
    });
    });
    