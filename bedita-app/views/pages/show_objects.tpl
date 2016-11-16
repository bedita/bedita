<script type="text/javascript">
<!--

if (typeof urlAddObjToAss{$relation|default:'norelations'|capitalize|replace:'-':'_'|escape:"javascript"} == "string") {
	var urlToAdd = urlAddObjToAss{$relation|default:''|capitalize|replace:'-':'_'|escape:"javascript"};
} else if (typeof urlAddObjToAss{$objectType|default:''|capitalize|escape:"javascript"} == "string") {
	var urlToAdd = urlAddObjToAss{$objectType|default:''|capitalize|escape:"javascript"};
} else if (typeof urlAddObjToAss == "string") {
	var urlToAdd = urlAddObjToAss;
} else {
	var urlToAdd = "{$html->url('/pages/loadObjectToAssoc')}";
}

var relType = "{$relation|default:""|escape:"javascript"}";
var suffix = "{$relation|default:""|capitalize|replace:'-':'_'|escape:"javascript"}";
var typesuffix = "{$objectType|default:'related'|capitalize|escape:"javascript"}";

/**
 * handle a list of object's ids
 * used to track checked objects also with pagination
 */
var objectsChecked = new ListHandler();

/**
 * ajax load objects' list to associate to main object
 *
 * @param  integer page The page number
 * @param  Array itemsToCheck List of objects' ids to check automatically
 */
function loadObjToAssoc(page, itemsToCheck) {
	var options = {
		target: '#assocObjContainer',
		beforeSubmit: function() {
			$('#assocObjContainer').empty();
			$("#modalmain .loader").show();
		},
		success: function() {
			$("#modalmain .loader").hide();
			// reset cleanFilter
			$("input[name=cleanFilter]", "#formFilter").val('');
			if (typeof itemsToCheck != 'undefined') {
				objectsChecked.add(itemsToCheck);
			}
			var listIds = objectsChecked.get();
			for (var i in listIds) {
				$('#objtable').find('input[type=checkbox][value=' + listIds[i] + ']').prop('checked', true);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#modalmain .loader").hide();
			console.error('error loading object list: ' + textStatus + ', ' + errorThrown);
		},
		data: {
			page: page
		}
	}

	$("#formFilter").ajaxSubmit(options);
}

/**
 * options to init select2 simple
 * to leave out of dom ready to have visibility in nested items
 */
var select2optionsSimple = {
	dropdownAutoWidth :true
}

/**
 * options to init select2 in tree mode
 * to leave out of dom ready to have visibility in nested items
 */
var select2optionsTree = {
	escapeMarkup: function(m) {
		return $('<div/>').html(m).text();
	},
	formatResult: function(state) {
		// escape html tags
        state.text = $('<div/>').html(state.text).text();
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
}

$(document).ready(function() {

	/*$('#modal .indexlist').each(function() {
		var $t = $(this);
        $t
            .width( $(this).closest('.mainfull').outerWidth() )
            .floatThead({
            	scrollContainer: function($table){
					return $('#modalmain .bodybg');
				}
            });

            $('#modalmain .floatThead-wrapper').css('height', '100%')
    });*/
	
	$("#formFilter").submit(function() {
		loadObjToAssoc(1);
		return false;
	});
	
	$("#addButton").click(function() {
		obj_sel = { relation: relType};
		obj_sel.object_selected = objectsChecked.get().join(',');

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
				if (eval("typeof addObjToAssoc" + typesuffix) == 'function') {
					eval("addObjToAssoc" + typesuffix)(urlToAdd, obj_sel);
				} else {
					console.log('function not found');
				}
			}
			
		}
	});

	$("select").not('.areaSectionAssociation, [name="filter[parent_id]"]').select2(select2optionsSimple);

	$('#modal select.areaSectionAssociation, [name="filter[parent_id]"]').select2(select2optionsTree);
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
			'customProp' => false,
			'relations' => true,
			'tags' => true,
			'editorial' => true
		]])}
	</div>

	<div class="trigger">{t}Create new{/t}</div>
	<div class="quick">
	{assign_associative var=quickParams ajax=true}
	{$view->element('quick_item', $quickParams)}
	</div>

	{$multimediaIds = array_intersect($objectTypeIds, $conf->objectTypes.multimedia.id)}
	{if !empty($objectTypeIds) && !empty($multimediaIds)}
		<div class="trigger">{t}Create many{/t}</div>
		<div class="quickmedia">
		{$view->element('quick_item_media')}
		</div>
	{/if}


	<div class="loader"></div>
	
	<div id="assocObjContainer">
		{include file="list_contents_to_assoc.tpl"}
	</div>

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