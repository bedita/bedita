<script type="text/javascript">
<!--
var urlShowObj = "{$html->here}";

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
	$("#loadObjInModal").show();
	$("#assocObjContainer").empty().load(urlShowObj, 
			{
				"parent_id": $("#parent_id").val(),
				"objectType": $("#objectType").val(),
				"lang": $("#lang").val(),
				"search": $("#search").val(),
				"page": page
			},
			function() {
				$("#loadObjInModal").hide();
	});
}

$(document).ready(function() {

	$("select[multiple]").bsmSelect();

	$(".searchTrigger").click(function() {
		$(".search").toggle('fast');
	});
	
	
	$("#searchButton").click(function() {
		loadObjToAssoc(1);
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

});

//-->
</script>

<div class="bodybg">

<div class="searchTrigger" style="
background:white url('{$html->webroot}img/piumeno.gif') no-repeat left 2px; 
padding:5px 0px 5px 30px; margin-bottom:1px; font-weight:bold; cursor:pointer;">
	{t}Search{/t} 
</div>

<div class="search" style="display:none; padding:10px; border:0px solid red;">
	
	{$view->element("filters_form",[
	'filters' => [
		'word' => true, 
		'tree' => true,
		'treeDescendants' => false,
		'type' => true,
		'language' => true,
		'customProp' => false
	]])}
	
</div>
	
	<div id="loadObjInModal" class="loader"><span></span></div>
	
	<div id="assocObjContainer">
		{include file="list_contents_to_assoc.tpl"}
	</div>

	<div class="modalcommands">
		
		<input type="button" id="addButton" style="width:300px" value=" {t}add{/t} ">

		{if !empty($html->params.named.group)}
 		<select title="{t}add permission{/t}" multiple id="modalSelectGroupPermission" name="permission[]">
            {foreach $conf->objectPermissions as $permLabel => $permVal}
            <option value="{$permVal}">{t}{$permLabel}{/t}</option>
            {/foreach}
        </select>
        {/if}
	
	</div>
</div>