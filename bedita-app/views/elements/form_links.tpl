{if isset($moduleList.webmarks)}

<script type="text/javascript">
<!--

var urlBaseAddLink = "{$html->url('/pages/addLink')}";

function addItem() {
	
	var divToFill = "#listExistingLinks";
	$("#loadingLinks").show();
	var emptyLI = "<tr><\/tr>"; 
	var linkTitle=$("#linkTitle").val();
	var linkUrl=$("#linkUrl").val();
	var target=$("#linkTarget").val();

	var postData = {
		'title': linkTitle,
		'url': linkUrl,
		'target': target
	};

	postData = addCsrfToken(postData);
	
	$(emptyLI).load(urlBaseAddLink, postData, function () {
		
		$("#listExistingLinks").append(this).fixItemsPriority() ; 
	
		$("#loadingLinks").hide();
		$(this).find("input[type='button']").click(function() {
			
			$(this).parents("tr").remove();
			$("#listExistingLinks").fixItemsPriority();
			
		});
	}) ;
}


$(document).ready(function() {
	$("#addLink").click(function () {
		addItem();
		$(".new").val('');
	});
	
	$("#listExistingLinks .remove").click(function() {
		
		$(this).parents("tr").remove();
		$("#listExistingLinks").fixItemsPriority();
	
	});
	
	$("#listExistingLinks").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	});
	
});

//-->
</script>

{$relcount = $relObjects.link|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Links{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="links">
	
	<input type="hidden" name="data[RelatedObject][link][0][switch]" value="link" />


	<table border="0" class="indexlist" >
		<thead>
			<tr>
				<th></th>
				<th>{t}title{/t}</th>
				<th>url</th>
				<th></th>
			</tr>
		</thead>
		<tbody id="listExistingLinks">
			{if isset($relObjects.link)}
			
				{foreach from=$relObjects.link item="objRelated" name="linkForeach"}
					{assign_associative var="params" objRelated=$objRelated}
					<tr>{$view->element('form_link_item',$params)}</tr>
				{/foreach}
			
			{/if}
		</tbody>
	
	{assign var="prior" value=$smarty.foreach.linkForeach.total|default:0}
		<tfoot>
			<tr id="loadingLinks" style="display:none">
				<td></td><td colspan="3">loading...</td>
			</tr>
			<tr id="newLink">
				<td>
					<input type="text" class="priority link" name="linkPriority" value="{$prior+1}" size="3" maxlength="3"/>
				</td>
				<td><input type="text" class="new" style="width:140px" name="linkTitle" id="linkTitle" /></td>
				<td><input type="text" class="new" style="width:230px" name="linkUrl" id="linkUrl" /></td>
				<td style="text-align: right"><input type="button" value="{t}add{/t}" id="addLink"/></td>
		
			</tr>
		</tfoot>	
	</table>
	
</fieldset>

{/if}

