var handleDatePickers = function () {

  $(".form_datetime").datetimepicker({
      autoclose: true,
      isRTL: false,
      format: "yyyy-mm-dd hh:ii",
      pickerPosition:   "bottom-left"
  });



}


jQuery(document).ready(function() {
	handleDatePickers();
});