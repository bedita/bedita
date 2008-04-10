<script type="text/javascript">
<!--

var urlBaseAddItem = "{$html->url('/documents/addLink/')}";

{literal}

// Get data from modal window, uploaded files and insert new object in the form
function addItem() {
	var divToFill = "#listExistingLinks";
	$("#loadingLinks").show();
	var emptyDiv = "<div><\/div>"; 
	var linkTitle=$("#linkTitle").val();
	var linkUrl=$("#linkUrl").val();
	var target=$("#linkTarget").val();
	$(emptyDiv).load(urlBaseAddItem, {'title': linkTitle, 'url':linkUrl, 'target':target }, function () {
		$(divToFill).append(this) ; 
		$(divToFill).reorderListItem();
		$("#loadingLinks").hide();
		$(this).find("input[@type='button']").click(function() {
			$(this).parents(".itemBox").remove();
			$(divToFill).reorderListItem();
		});
	}) ;
	
	try { $().alertSignal() ; } catch(e) {}
}


$(document).ready(function() {
	$("#addLink").click(function () {
		addItem();
	});
	$(".itemBox").each(function(index) { 
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[ObjectRelation]["+index+"][id]") ;
		$(".switch", this).attr("name", "data[ObjectRelation]["+index+"][switch]") ;
		$(".priority", this).attr("name", "data[ObjectRelation]["+index+"][priority]") ;
	}) ;
	$("#listExistingLinks").find("input[@type='button']").click(function() {
		$(this).parents(".itemBox").remove();
		$("#listExistingLinks").reorderListItem();
	});
	
});
{/literal}
//-->
</script>


<h2 class="showHideBlockButton">{t}Links{/t}</h2>
<div class="blockForm" id="links">
	<div id="listExistingLinks" style="min-height:100px">
	{if isset($relObjects.link)}
	{foreach from=$relObjects.link item="objRelated" name="linkForeach"}
		{include file="../pages/form_link_item.tpl"}
	{/foreach}
	{/if}
	</div>
	<div id="newLink">
	<fieldset>
	{t}Title{/t}<input type="text" name="linkTitle" id="linkTitle"/>
	{t}Url{/t}<input type="text" name="linkUrl" id="linkUrl"/>
	{t}Type{/t}<select name="targetType" id="linkTarget"> 
		<option value="_self">_self</option>
		<option value="_blank">_blank</option>
	</select>
	<input type="button" value="{t}Add{/t}" id="addLink"/>
	</fieldset>
	</div>
	<div id="loadingLinks" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>
</div>