// Customize two date pickers to work as a date range 
function customRange(input) { 
    return {minDate: (input.id == 'end' ? $('#start').getDatepickerDate() : null), 
        	maxDate: (input.id == 'start' ? $('#end').getDatepickerDate() : null)}; 
}

// build or refresh tree
function designTreeWhere() {
	$("#treeWhere").Treeview({
		control: "#treecontrol" ,
		speed: 'fast',
		collapsed:false
	});
}

// add checkbutton in areas'tree
function addCommandWhere() {
	$("span[@class='SectionItem'], span[@class='AreaItem']", "#treeWhere").each(function(i) {
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
		
		if(parents.indexOf(parseInt(id)) > -1) {
			$(this).before('<input type="checkbox" name="data[destination][]" id="s_'+id+'" value="'+id+'" checked="checked"/>&nbsp;');
		} else {
			$(this).before('<input type="checkbox" name="data[destination][]" id="s_'+id+'" value="'+id+'"/>&nbsp;');			
		}
		
		$(this).html('<label class="section" for="s_'+id+'">'+$(this).html()+"<\/label>") ;
	}) ;
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