   
    <script src="assets/js/bootstrap.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
     <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
     <script>
       feather.replace()
     </script>
     <script type="text/javascript">
	$(document).ready(function(){ 
      var $disabledResults = $(".js-example-disabled-results");
        $disabledResults.select2();

        $('.js-example-basic-multiple').select2();

/*		  $('.textonly').keydown(function (e) {
  			 if ( e.ctrlKey || e.altKey) {
		      e.preventDefault();
		    } else {
		      var key = e.keyCode;
		      if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
		        e.preventDefault();
		    
		      }
		    }
    
 		 });*/
    $(".textonly").keypress(function(event){
        var inputValue = event.charCode;
        if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)){
            event.preventDefault();
        }
    });
	jQuery('.uppercase').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });
});
	


         jQuery(document).ready(function(e) {

            $('#change-password-form').on('submit',function(e) {
                e.preventDefault();
                $.ajax({  
                    url:"ajax/ajaxChangePassword.php",  
                    type: "POST",
                    data:new FormData(this), 
                    contentType: false,
                    cache: false,
                    processData:false, 
                    beforeSend:function(){  
                       // $('#response').html('<span class="text-info">Loading response...</span>');  
                       $('#loader-main').show();   
                    },  
                    success:function(data){  
                      $('form').trigger("reset");  
                      $('#change-password').modal('hide');
                      //$('#response').fadeIn().html(data);  
                      /*setTimeout(function(){  
                      $('#response').fadeOut("slow");  
                      }, 5000);  */
                      $('#loader-main').hide();   
                     
                       alert(data);
                        //window.location.href='disability.php?member_id='+data;
                     
                     }  
                  }); 
               });
        });
</script>


