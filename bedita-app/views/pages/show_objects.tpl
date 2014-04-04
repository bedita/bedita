<script type="text/javascript">
<!--
//var urlShowObj = "{$html->here}";

if (typeof urlAddObjToAss{$relation|default:'norelation'|capitalize} == "string") {
	var urlToAdd = urlAddObjToAss{$relation|capitalize}
} else if (typeof urlAddObjToAss == "string") { 
	var urlToAdd = urlAddObjToAss;
} else {
	var urlToAdd = "{$html->url('/pages/loadObjectToAssoc')}";
}

var relType = "{$relation|default:""}";
var suffix = "{$relation|default:""|capitalize}";


function loadObjToAssoc(page) {

	var options = {
		target: '#assocObjContainer',
		beforeSubmit: function() {
			$('#assocObjContainer').empty();
			$("#loadObjInModal").show();
		},
		success: function() {
			$("#loadObjInModal").hide();
			// reset cleanFilter
			$("input[name=cleanFilter]", "#formFilter").val('');
		},
		data: {
			page: page
		}
	}

	$("#formFilter").ajaxSubmit(options);
}

$(document).ready(function() {

	$(".trigger").click(function() {
		$(this).next().toggle('fast');
	});
	
	$("#formFilter").submit(function() {
		loadObjToAssoc(1);
		return false;
	});
	
	$("#addButton").click(function() {
		obj_sel = { relation: relType};
		obj_sel.object_selected = "";
		
		$("#assocObjContainer :checked").each(function() {
			obj_sel.object_selected += $(this).val() + ","; 
		});

		if ($("#modalSelectGroupPermission").length > 0) {
			obj_sel.permission = $("#modalSelectGroupPermission").val();
		}
		
		if (obj_sel.object_selected != "") {
			
			$("#modal").hide();
			$("#modaloverlay").hide();
			
			// if addObjToAssoc + suffix is defined use it (i.e. addObjToAssocQuestion in questionnaires/form_list_questions.tpl)
			// else addObjToAssoc function has to be defined in other template (i.e. elements/form_assoc_objects.tpl)
			if (eval("typeof addObjToAssoc" + suffix) == 'function') {
				eval("addObjToAssoc" + suffix)(urlToAdd, obj_sel);
			} else {
				addObjToAssoc(urlToAdd, obj_sel);
			}
			
		}
	});

	$("select").not('.areaSectionAssociation, [name="filter[parent_id]"]').select2({
		dropdownAutoWidth:true,
		allowClear: true
	});

	$('.modal select.areaSectionAssociation, [name="filter[parent_id]"]')
		.select2({
			escapeMarkup: function(m) { return m; },
			formatResult: function(state) {
				if ($(state.element).is('.pubOption')) {
					return '<a rel="'+$(state.element).attr('rel')+'" onmouseup="toggleSelectTree(event)">> </a>'+state.text;
				} else {
					if (!$(state.element).is(':first-child')) {
						var ar = state.text.split(' > ');
						var last = ar.pop();
						return '<span class="gray">'+ar.join(' > ')+' > </span>'+last;
					} else {
						return state.text;
					}
				}
			}
		});
});

//-->
</script>

<div class="bodybg">

	<div class="trigger">{t}Search{/t}</div>
	<div class="search" >
		{$view->element("filters_form",[
		'filters' => [
			'word' => true, 
			'tree' => true,
			'treeDescendants' => true,
			'type' => true,
			'language' => true,
			'customProp' => false
		]])}
	</div>

	<div class="trigger">{t}Create new{/t}</div>
	<div class="quick">
	{$view->element('quick_item')}
	</div>

	<div class="trigger">{t}Create many{/t}</div>
	<div class="quickmedia">
	{$view->element('quick_item_media')}
	</div>


	<div id="loadObjInModal" class="loader"><span></span></div>
	
	<div id="assocObjContainer">
		{include file="list_contents_to_assoc.tpl"}
	</div>
	
	<div class="modalcommands">
		<input type="button" id="addButton" style="margin-bottom:10px; width:300px" value=" {t}add{/t} ">
		{if !empty($html->params.named.group)}
 		<select title="{t}permission type{/t}" multiple id="modalSelectGroupPermission" name="permission[]" data-placeholder="{t}select a permission{/t}">
            {foreach $conf->objectPermissions as $permLabel => $permVal}
            <option value="{$permVal}">{t}{$permLabel}{/t}</option>
            {/foreach}
        </select>
        {/if}
	</div>

</div>