{* included by show_objects.tpl *}

{$html->script("jquery/jquery.tablesorter.min")}

<script type="text/javascript">
<!--
$(document).ready(function() {

	$("#contents_nav a").click(function() {
		loadObjToAssoc($(this).attr("rel"));
	});

	 $("#objtable").tablesorter(); 	
	 $("#objtable thead TH").css("cursor","pointer"); 

});
//-->
</script>

{if !empty($objectsToAssoc.items)}
	
	<table class="indexlist" id="objtable">
	<thead>
		<tr>
			<th></th>
			<th>{t}title{/t}</th>
			<th style="text-align:center">{t}type{/t}</th>
			<th style="text-align:center">{t}status{/t}</th>
			<th style="text-align:center">{t}modified{/t}</th>
			<th style="text-align:center">{t}lang{/t}</th>
			<th style="text-align:center">Id</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$objectsToAssoc.items item="objToAss"}
		<tr>
			<td style="width:15px; vertical-alig:middle; padding:0px 0px 0px 10px;">
				<input type="checkbox" name="object_selected[]" class="objectCheck" value="{$objToAss.id}"/>
			</td>
			<td>{$objToAss.title|default:'<i>[no title]</i>'}</td>
			<td style="padding:0px; width:10px;">
				<span class="listrecent {$objToAss.moduleName}" style="margin:0px 0px 0px 10px">&nbsp;</span>
			</td>
			<td style="text-align:center">{$objToAss.status}</td>
			<td style="text-align:center">{$objToAss.modified|date_format:$conf->datePattern}</td>
			<td style="text-align:center">{$objToAss.lang}</td>
			<td style="text-align:center">{$objToAss.id}</td>
		</tr>
		{/foreach}
	</tbody>
	</table>


	<div id="contents_nav" class="graced" 
	style="text-align:center; color:#333; font-size:1.1em;  margin:25px 0px 1px 0px; background-color:#FFF; padding: 5px 10px 10px 10px;">
		
		{$objectsToAssoc.toolbar.size} {t}items{/t} | {t}page{/t} {$objectsToAssoc.toolbar.page} {t}of{/t} {$objectsToAssoc.toolbar.pages} 

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
	<div style="background-color:#FFF; padding:20px;">{t}No item found{/t}</div>
{/if}