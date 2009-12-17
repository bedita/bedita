<script type="text/javascript">
<!--
var urlShowObj = "{$html->here}";
{literal}
if (typeof urlAddObjToAss{/literal}{$relation|default:'norelation'|capitalize}{literal} == "string") {
	var urlToAdd = urlAddObjToAss{/literal}{$relation|capitalize}{literal}
} else if (typeof urlAddObjToAss == "string") { 
	var urlToAdd = urlAddObjToAss;
} else {
	var urlToAdd = "{/literal}{$html->url('/pages/loadObjectToAssoc')}{literal}";
}
{/literal}
var relType = "{$relation|default:""}";
var suffix = "{$relation|default:""|capitalize}";

{literal}
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


	$(".searchTrigger").click(function() {
		$(".search").toggle('fast');
	});
	
	
	$("#searchButton").click(function() {
		loadObjToAssoc(1);
	});
	
	
	$("#searchButton").click(function() {
		loadObjToAssoc(1);
	});
	
	$("#addButton").click(function() {
		obj_sel = {relation: relType};
		obj_sel.object_selected = "";
		
		$("#assocObjContainer :checked").each(function() {
			obj_sel.object_selected += $(this).val() + ","; 
		});
		
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
{/literal}
//-->
</script>

<div class="bodybg">

<div class="searchTrigger" style="
background:white url('{$html->webroot}img/piumeno.gif') no-repeat left 2px; 
padding:5px 0px 5px 30px; margin-bottom:1px; font-weight:bold; cursor:pointer;">
	{t}Search{/t} 
</div>

<div class="search" style="display:none; padding:10px; border:0px solid red;">
	
	<table>
		<tr>
			<th><label>{t}word{/t}:</label></th>
			<td><input type="text" name="search" id="search" value="" /></td>
			<th><label>{t}type{/t}:</label></th>
			<td>
				<select name="objectType" id="objectType">
					<option value="">{t}all{/t}</option>
					{foreach from=$objectTypeIds item=type_id}
						{if $type_id}
						{strip}
						<option value="{$type_id}">
							{$conf->objectTypes[$type_id].name|lower}
						</option>
						{/strip}
						{/if}
					{/foreach}
				</select>
			</td>
			<td rowspan="2">
				<input type="button" id="searchButton" value=" {t}Find it{/t} ">
			</td>
		</tr>
		<tr>
			<th><label>{t}on{/t}:</label></th>
			<td>
				<select style="width:180px" name="parent_id" id="parent_id">
				{$beTree->option($tree)}
				</select>
			</td>
			<th><label>{t}language{/t}:</label></th>
			<td>
					<select name="lang" id="lang">
					<option value="">{t}all{/t}</option>
					{foreach key=val item=label from=$conf->langOptions}
						{strip}
						<option value="{$val}">
							{$label}
						</option>
						{/strip}
					{/foreach}
				</select>
			</td>
			
		</tr>
	</table>


</div>
	
	<div id="loadObjInModal" class="loader"><span></span></div>
	
	<div id="assocObjContainer">
		{include file="list_contents_to_assoc.tpl"}
	</div>

	<div class="modalcommands">
		
		<input type="button" id="addButton" style="width:300px" value=" {t}add{/t} ">
	
	</div>
</div>



	