require(['jquery'],function($){

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