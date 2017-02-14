var handleDatePickers = function () {

  if (jQuery().datepicker) {
    $('.date-picker').datepicker({
      rtl: false,
      format: 'yyyy-mm-dd',
      orientation: "left",
      autoclose: true
    }).on('changeDate', function(e) {
      //window.location.href = '//' + location.host + location.pathname + '?date=' + moment(e.date).format('YYYY-MM-DD')  ;
      $(this).parents('form:first').submit();
    });
    //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
  }

  $('select[name="employee_type"]').change(function(e) {
    $(this).parents('form:first').submit();
  });

}

jQuery(document).ready(function() {
	handleDatePickers();


});