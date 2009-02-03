{* included by show_objects.tpl *}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	$("#contents_nav a").click(function() {
		loadObjToAssoc($(this).attr("rel"));
	});

});
{/literal}
//-->
</script>

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
				<input type="checkbox" name="object_selected[]" class="objectCheck" value="{$objToAss.id}"/>
			</td>
			<td><a href="">{$objToAss.id}</a></td>
			<td><a href="">{$objToAss.title|default:'<i>[no title]</i>'}</a></td>
			<td>
				<span style="margin:0" class="listrecent {$objToAss.moduleName}">&nbsp;</span>
			</td>
			<td>{$objToAss.status}</td>
			<td>{$objToAss.created|date_format:$conf->datePattern}</td>
			<td>{$objToAss.lang}</td>
		</tr>
		{/foreach}

	</table>

	<br />
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
	{t}No item found{/t}
{/if}