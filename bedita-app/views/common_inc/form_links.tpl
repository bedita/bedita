<script type="text/javascript">
<!--

var urlBaseAddLink = "{$html->url('/pages/addLink')}";

{literal}

function addItem() {
	var divToFill = "#listExistingLinks";
	$("#loadingLinks").show();
	var emptyLI = "<li class='itemBox'><\/li>"; 
	var linkTitle=$("#linkTitle").val();
	var linkUrl=$("#linkUrl").val();
	var target=$("#linkTarget").val();
	$(emptyLI).load(urlBaseAddLink, {'title': linkTitle, 'url':linkUrl, 'target':target }, function () {
		$("#listExistingLinks").append(this).fixItemsPriority() ; 
		
		$("#loadingLinks").hide();
		$(this).find("input[@type='button']").click(function() {
			$(this).parents("li").remove();
			$("#listExistingLinks").fixItemsPriority();
		});
	}) ;
}


$(document).ready(function() {
	$("#addLink").click(function () {
		addItem();
	});
	
	$("#listExistingLinks").find("input[@type='button']").click(function() {
		$(this).parents("li").remove();
		$("#listExistingLinks").fixItemsPriority();
	});
	
	$("#listExistingLinks").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	});
	
});

{/literal}
//-->
</script>

<div class="tab"><h2>{t}Links{/t}</h2></div>

<fieldset id="links">
	
	<input type="hidden" name="data[RelatedObject][link][0][switch]" value="link" />


	<ul id="listExistingLinks">

	{if isset($relObjects.link)}
	{foreach from=$relObjects.link item="objRelated" name="linkForeach"}
		<li class="itemBox">{include file="../common_inc/form_link_item.tpl"}</li>
	{/foreach}
	{/if}
	
	</ul>

	<hr />
	{assign var="prior" value=$smarty.foreach.linkForeach.total|default:0}
	<div id="newLink" style="white-space:nowrap">
		<input type="text" class="priority" style="margin-left:-10px;" 
		name="linkPriority" value="{$prior+1}" size="3" maxlength="3"/>
		<label>{t}title{/t}:</label> 	<input type="text" style="width:150px" name="linkTitle" id="linkTitle" />
		<label>{t}url{/t}:</label> 	<input type="text" style="width:200px" name="linkUrl" id="linkUrl" />
		{*
		<label>{t}target{/t}:</label> 	<select name="targetType" id="linkTarget"> 
						<option value="_self">_self</option>
						<option value="_blank">_blank</option>
						</select>
		*}
		<input type="button" value="{t}+{/t}" id="addLink"/>
	</div>
	
	<div id="loadingLinks" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>


	
</fieldset>
