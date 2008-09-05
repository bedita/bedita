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
		$("#listExistingLinks").append(this).reorderListItem() ; 
		
		$("#loadingLinks").hide();
		$(this).find("input[@type='button']").click(function() {
			$(this).parents("li").remove();
			$("#listExistingLinks").reorderListItem();
		});
	}) ;
}


$(document).ready(function() {
	$("#addLink").click(function () {
		addItem();
	});
	
	$("#listExistingLinks").find("input[@type='button']").click(function() {
		$(this).parents("li").remove();
		$("#listExistingLinks").reorderListItem();
	});
	
	$("#listExistingLinks").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
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
	
	<div id="newLink" style="white-space:nowrap">
		<label>{t}title{/t}:</label> 	<input type="text" style="width:100px" name="linkTitle" id="linkTitle" />
		<label>{t}url{/t}:</label> 	<input type="text" name="linkUrl" id="linkUrl" />
		<label>{t}target{/t}:</label> 	<select name="targetType" id="linkTarget"> 
						<option value="_self">_self</option>
						<option value="_blank">_blank</option>
						</select>
		<input type="button" value="{t}Add{/t}" id="addLink"/>
	</div>
	
	<div id="loadingLinks" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>


	
</fieldset>
