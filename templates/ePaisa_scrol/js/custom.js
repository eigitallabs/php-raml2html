$( document ).ready(function() {



/*$('.endpoint').click(function () {
   event.preventDefault();
   var endpoind_path = $(this).attr('id');
   var final_path = endpoind_path.replace('menu', '');


  $("#main-content").animate({
        scrollTop: $("#"+final_path).offset().top-65
    },700);
 
 return false;
 

   
});*/

/*$('.endpoint_click').click(function () {

	 var endpoind_path = $(this).attr('id');
    window.location.hash = endpoind_path;


   $("#main-content").html("<div class='uk-flex uk-flex-center' style='height: 100vh;'><div uk-spinner style='width:120px !important; vertical-align: middle; margin-top: 150px;'></div></div>");
     

    /*Ajax call to display endpoint details based on menu item clicked*/
 /*$.ajax({
      url: 'http://localhost/php-raml2html/ajax_call.php',
      //url: 'http://52.66.91.145/api/ajax_call.php',
      type: 'post',
      data: {'endpoind': endpoind_path},
      success: function(data, status) {

          
          $("#main-content").html(data);

      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    }); // end ajax call
  return false;
});*/
$('#mobile_menu_select').on('change', function (e) {

       $target = '#'+$(this).val();
       $('html, body').animate({
        scrollTop: $($target).offset().top - 90
    }, 'slow');
      
    });



$('.verb_name').click(function (){
  
   var verb_name_id = $(this).attr('id')
   
   var id = $(this).parents('.endpoint_holder').attr('id')

   $('#'+id).find('.code').each(function () {
    $(this).removeClass("show_code");
    $(this).addClass("hide_code");
    });

   $('#code_holder_'+id+"_"+verb_name_id).removeClass("hide_code");
   $('#code_holder_'+id+"_"+verb_name_id).addClass("show_code");


   });
   

});