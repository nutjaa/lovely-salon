var handleDatePickers = function () {

  if (jQuery().datepicker) {
    $('.date-picker').datepicker({
      rtl: false,
      format: 'yyyy-mm-dd',
      orientation: "left",
      autoclose: true
    }).on('changeDate', function(e) {
      window.location.href = '//' + location.host + location.pathname + '?date=' + moment(e.date).format('YYYY-MM-DD')  ;
    });
    //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
  }

  //click column to dit ;

  $('.task-col').click(function(){
    var id = $(this).data('id');
    window.location.href = '//' + location.host + '/'+shop_url+'/daily-jobs/' + id ;
  });


}

jQuery(document).ready(function() {
	handleDatePickers();


});