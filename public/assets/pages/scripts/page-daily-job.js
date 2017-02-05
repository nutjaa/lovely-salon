var handleDatePickers = function () {

  $(".form_datetime").datetimepicker({
      autoclose: true,
      isRTL: false,
      format: "yyyy-mm-dd hh:ii",
      pickerPosition:   "bottom-left"
  });

}

var handleTwitterTypeahead = function(){
	var customers = new Bloodhound({
    datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 10,
    prefetch: {
      cache: false,
      url: '/'+shop_url+'/customers/listing',
      filter: function(list) {
        return $.map(list, function(customer) { return { name: customer }; });
      }
    }
  });

  customers.initialize();

  $('#customer_name').typeahead(null, {
    name: 'customer_name',
    displayKey: 'name',
    source: customers.ttAdapter()
  });
}

jQuery(document).ready(function() {
	handleDatePickers();
	handleTwitterTypeahead();
});