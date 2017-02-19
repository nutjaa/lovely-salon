

jQuery(document).ready(function() {
	$('select[name="date_range_id"] , select[name="employee_id"]').change(function(e) {
    $(this).parents('form:first').submit();
  });
});