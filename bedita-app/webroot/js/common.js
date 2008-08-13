/**
 * Common JavaScript functions
 * 
 * (some to be moved to beditaUI.js)
 *  
 */


/*
 * General functions
 */

// Date - force two date pickers to work as a date range 
function customRange(input)
{
    return {minDate: (input.id == 'end' ? $('#start').datepicker( "getDate" ) : null), 
        	maxDate: (input.id == 'start' ? $('#end').datepicker( "getDate" )  : null)}; 
}




/*
 * Extend JQuery
 */
 
jQuery.fn.extend({
	check: function() {
		return this.each(function() { this.checked = true; });
	},
	uncheck: function() {
		return this.each(function() { this.checked = false; });
	},
	toggleCheck: function() {
		return this.each(function() { this.checked = !this.checked ; });
	},
	submitConfirm: function(params) {
		$(this).bind("click", function() {
			if(!confirm(params.message)) {
				return false ;
			}
			if (params.formId) {
				$("#" + params.formId).attr("action", params.action);
				$("#" + params.formId).submit(); 
			} else {
				$(this).parents("form").attr("action", params.action);
				$(this).parents("form").submit();
			}
		});
	},
	
	/*
	*	reorder items
	*/
	reorderListItem: function ()
	{
		$(this).find(".itemBox").each(function (priority)
		{
			$(this).find ("input[@name*='[priority]']").val (priority+1)	// update priority
				.hide().fadeIn(100).fadeOut(100).fadeIn('fast');			// effects
		});
	}

});

