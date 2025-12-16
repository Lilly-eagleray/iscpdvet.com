jQuery(document).ready(function( $ ) {
$(".export_to_excel").click(function(){
  $(".table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Worksheet Name",
    filename: "SomeFile" //do not include extension
  }); 
});

if(jQuery('.select-t').length > 0 ){
    jQuery('.select-t').select2();
}


});