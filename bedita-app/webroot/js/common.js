// Customize two date pickers to work as a date range 
function customRange(input) { 
    return {minDate: (input.id == 'end' ? $('#start').getDatepickerDate() : null), 
        	maxDate: (input.id == 'start' ? $('#end').getDatepickerDate() : null)}; 
}

// jquery functions
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
			$(this).parents("form").attr("action", params.action);
			$(this).parents("form").submit();
		})
	},
	// require treeview plugin
	designTree: function(url) {
		$(this).Treeview({
			control: false ,
			speed: 'fast',
			collapsed:false
		});
		
		$("li span", this).each(function(i){
			// get ID section 
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
	
			// add anchor
			$(this).html('<a href="'+url+"id:"+id+'">'+$(this).html()+'</a>') ;
		});
	}
});