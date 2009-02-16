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
			<th>title</th>
			<th style="text-align:center">type</th>
			<th style="text-align:center">status</th>
			<th style="text-align:center">date</th>
			<th style="text-align:center">lang</th>
			<th>Id</th>
		</tr>

		{foreach from=$objectsToAssoc.items item="objToAss"}
		<tr>
			<td style="width:15px; padding:7px 0px 0px 10px;">
				<input type="checkbox" name="object_selected[]" class="objectCheck" value="{$objToAss.id}"/>
			</td>
			<td>{$objToAss.title|default:'<i>[no title]</i>'}</td>
			<td style="text-align:center">
				<span style="margin:0px" class="listrecent {$objToAss.moduleName}">&nbsp;</span>
			</td>
			<td style="text-align:center">{$objToAss.status}</td>
			<td>{$objToAss.created|date_format:$conf->datePattern}</td>
			<td style="text-align:center">{$objToAss.lang}</td>
			<td style="text-align:center">{$objToAss.id}</td>
		</tr>
		{/foreach}

	</table>


	<div id="contents_nav" class="graced" 
	style="font-size:1.275em; padding:10px 10px 0px 10px;">
		
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