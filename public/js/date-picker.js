$(document).ready(function(){

  $("#datepicker").datepicker({
    firstDay: 1,
    showOtherMonths: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy"
  });
  
  $(".date").mousedown(function() {
    $(".ui-datepicker").addClass("active");
  });


})