jQuery(document).ready(function( $ ) {
	
$( "#vettype" ).change(function() {
let type = $(this).val();
if(type == "vet"){
placeholder = ajax_object.vet;
$(".year").show();
}
else if(type == "student"){
placeholder = ajax_object.student;
$(".year").hide();
}
else if(type == "tech"){
placeholder = ajax_object.tech;
$(".year").hide();
}


  $("#vetid").attr("placeholder",placeholder);
});


$("#billing_email").keyup(function(event) {
  var stt = $(this).val();
  $("#account_username").val(stt);
});

$(".tipso").tipso({
  animationIn: 'bounceIn',
  animationOut: 'bounceOut'
});


});


