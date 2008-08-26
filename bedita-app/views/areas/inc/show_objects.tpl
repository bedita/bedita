
<div class="bodybg" style="{if empty($html->params.form)}padding:10px;{/if}" id="assocObjContainer">
	
	{* leave javascript into div to not duplicate code after ajax call *}
	<script type="text/javascript">
	<!--
	
	urlShowObj = "{$html->url('/areas/showObjects')}";
	
	{literal}
	function loadObjToAssoc(page) {
		$("#assocObjContainer").load(urlShowObj, 
				{
					"parent_id": $("#parent_id").val(),
					"objectType": $("#objectType").val(),
					"lang": $("#lang").val(),
					"search": $("#search").val(),
					"page": page
				},
				function() {
			
		});
	}
	
	$(document).ready(function() {
	
		$("#submitButton").click(function() {
			loadObjToAssoc(1);
		});

		$("#contents_nav a").click(function() {
			loadObjToAssoc($(this).attr("rel"));
		});
	
	});
	{/literal}
	//-->
	</script>

	<label>Cerca:</label> &nbsp; <input type="text" name="search" id="search" value="{$html->params.form.search|default:null}">
	&nbsp;&nbsp;
	in: <select name="parent_id" id="parent_id">
			{assign var="parent_id" value=$html->params.form.parent_id|default:null}
			{$beTree->option($tree, $parent_id)}
		</select>
	<hr>
	
	
	tipo: 
	<select name="objectType" id="objectType">
		<option value="">{t}all{/t}</option>
		{foreach from=$conf->objectTypes.related item=type_id}
			{strip}
			<option value="{$type_id}"{if $html->params.form.objectType|default:null == $type_id} selected{/if}>
				{$conf->objectTypeModels[$type_id]|lower}
			</option>
			{/strip}
		{/foreach}
		</select>
	
	&nbsp;&nbsp;
	lingua: 
	<select name="lang" id="lang">
		<option value="">{t}all{/t}</option>
		{foreach key=val item=label from=$conf->langOptions}
			{strip}
			<option value="{$val}"{if $html->params.form.lang|default:null == $val} selected{/if}>
				{$label}
			</option>
			{/strip}
		{/foreach}
	</select>
	
	&nbsp;&nbsp;
	<input type="button" id="submitButton" value=" {t}Search{/t} ">
	<hr />
	
	{if !empty($objectsToAssoc.items)}
	
		<table class="indexlist">
	
			<tr>
				<th></th>
				<th>Id</th>
				<th>title</th>
				<th>type</th>
				<th>status</th>
				<th>date</th>
				<th>lang</th>
			</tr>
	
		
			{foreach from=$objectsToAssoc.items item="objToAss"}
			
			<tr>
				<td style="width:15px; padding:7px 0px 0px 0px;">
					<input  type="checkbox" name="object_chk" class="objectCheck" />
				</td>
				<td><a href="">{$objToAss.id}</a></td>
				<td><a href="">{$objToAss.title}</a></td>
				<td>
					<span style="margin:0" class="listrecent {$objToAss.moduleName}">&nbsp;</span>
				</td>
				<td>{$objToAss.status}</td>
				<td>{$objToAss.created|date_format:$conf->datePattern}</td>
				<td>{$objToAss.lang}</td>
			</tr>
			{/foreach}
	
		</table>
	
		<hr />
		<div id="contents_nav">
			
			{t}Items{/t}: {$objectsToAssoc.toolbar.size} | {t}page{/t} {$objectsToAssoc.toolbar.page} {t}of{/t} {$objectsToAssoc.toolbar.pages} 
	
			{if $objectsToAssoc.toolbar.first > 0}
				&nbsp; | &nbsp;
				<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.first}" id="streamFirstPage" title="{t}first page{/t}">{t}first{/t}</a></span>
			{/if}			

			{if $objectsToAssoc.toolbar.prev > 0}
				&nbsp; | &nbsp;
				<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.prev}" id="streamPrevPage" title="{t}previous page{/t}">{t}prev{/t}</a></span>
			{/if}
	
			{if $objectsToAssoc.toolbar.next > 0}
				&nbsp; | &nbsp;
				<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.next}" id="streamNextPage" title="{t}next page{/t}">{t}next{/t}</a></span>
			{/if}
			
			{if $objectsToAssoc.toolbar.last > 0}
				&nbsp; | &nbsp;
				<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.last}" id="streamLastPage" title="{t}last page{/t}">{t}last{/t}</a></span>
			{/if}
			
											
		</div>
	
	{else}
		{t}No items founded{/t}
	{/if}
	

</div>