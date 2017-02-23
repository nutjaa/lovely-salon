

jQuery(document).ready(function() {
	$('select[name="monthly_select_id"]').change(function(e) {
    $(this).parents('form:first').submit();
  });
});