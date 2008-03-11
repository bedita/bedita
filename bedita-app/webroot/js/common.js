// Customize two date pickers to work as a date range 
function customRange(input) { 
    return {minDate: (input.id == 'end' ? $('#start').getDatepickerDate() : null), 
        	maxDate: (input.id == 'start' ? $('#end').getDatepickerDate() : null)}; 
}

/*
*	jQuery functions
*
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
			$(this).parents("form").attr("action", params.action);
			$(this).parents("form").submit();
		});
	},
	/*
	*	build the tree
	*	@params: params is a JSON object with values:
	*				- @id_control: div's id that contains "close all" and "expand all" anchors
	*				- @url: target base url of area/section
	*				- @inputTypt: input type to add to the item's tree (i.e. checkbox, radio)
	*/
	designTree: function(params) {
		controlElem = (params.id_control)? "#" + params.id_control : false ;
		$(this).Treeview({
			control: controlElem,
			speed: 'fast',
			collapsed:false
		});
		
		$("li span", this).each(function(i){
			// get ID section 
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			if (params.url) {
				// add anchor
				$(this).html('<a href="'+params.url+"id:"+id+'">'+$(this).html()+'</a>') ;
			} 
			if (params.inputType) {
				// add input
				$(this).before('<input type="' + params.inputType  + '" name="data[destination][]" id="s_'+id+'" value="'+id+'"/>&nbsp;');
				$(this).html('<label class="section" for="s_'+id+'">'+$(this).html()+"<\/label>") ;
				// if there isn't any radio button checked it checks the first
				if (params.inputType == "radio") { 
					if ($("input[name*='destination']:checked").length == 0) {
						$("input[name*='destination']:first").attr("checked", "checked"); 
					}
				}
			}
		});
	},
	/*
	*	change focus of each tab element in div (class="tabsContainer")
	*	require ui.tabs
	*/
	changeActiveTabs: function() {
		$(this).bind("click", function(){
			var index = $(this).parents("ul").data('selected.ui-tabs');
			$('div.tabsContainer > ul').tabs("select",index);
		});
	},
	enableDisableTabs: function() {
		$(this).bind("click", function(){
			var action = ($(this).attr('checked')) ? "enable" : "disable";
			var index = Number($(this).attr('title'));
			$('div.tabsContainer > ul').tabs(action,index);
		});
	},
	mainLang: function() {
		$(this).bind("change", function(){
			$('.lang_flags').attr('disabled',false);
			$('#flag_'+$(this).val()).attr('checked','checked');
			$('#flag_'+$(this).val()).attr('disabled','disabled');
			var index = Number($(this)[0].selectedIndex);
			$('div.tabsContainer > ul').tabs('enable',index);
		});
	}
});