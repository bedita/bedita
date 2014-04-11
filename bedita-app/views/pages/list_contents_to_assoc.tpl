{* included by show_objects.tpl *}

{$html->script("libs/jquery/plugins/jquery.tablesorter.min")}

<script type="text/javascript">
<!--
$(document).ready(function() {

	$("#contents_nav a").click(function() {
		loadObjToAssoc($(this).attr("rel"));
	});

	$("#objtable").tablesorter(); 	
	$("#objtable thead TH").css("cursor","pointer");

	$('#objtable').find('input[type=checkbox]').click(function() {
		var objectId = $(this).val();
		if ($(this).prop('checked')) {
			objectsChecked.add(objectId);
		} else {
			objectsChecked.remove(objectId);
		}
		// update add button
		var addLabel = $('#addButton').val();
		addLabel = addLabel.replace(/\s\d+\sitems/, '');
		var countIds = objectsChecked.get().length;
		if (countIds) {
			addLabel += ' ' + countIds + ' items';
		}
		$('#addButton').val(addLabel);
	});

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
			<th></th>
			<th style="text-align:center">{t}modified{/t}</th>
			<th style="text-align:center">{t}lang{/t}</th>
			<th style="text-align:center">Id</th>
			<th></th>
		</tr>
	</thead>
	<tbody>

	{assign_associative var="params" presentation="thumb" width='64'}

		{foreach from=$objectsToAssoc.items item="objToAss"}
		<tr>
			<td style="white-space:nowrap; width:15px; vertical-alig:middle; padding:0px 0px 0px 10px;">
				<input type="checkbox" name="object_selected[]" class="objectCheck" value="{$objToAss.id}"/>
				{if !empty($objToAss.num_of_permission)}
					<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; margin:0 0 0 -5px; vertical-align:top;">
				{/if}
				
				{if ($objToAss.ubiquity|default:0 > 1)}
					<img title="{t}ubiquous object{/t}" src="{$html->webroot}img/iconUbiquity.png" style="margin:4px 4px 0 0; height:18px; vertical-align:top;">
				{/if}
				{if (!empty($objToAss.fixed))}
					<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
				{/if}
			</td>
			<td>{$objToAss.title|default:'<i>[no title]</i>'}</td>
			<td style="padding:0px; width:10px;">
				<span class="listrecent {$objToAss.moduleName}">&nbsp;</span>
			</td>
			<td style="text-align:center">{$objToAss.status}</td>
			<td class="filethumb">
			<!-- {*if $objToAss.moduleName == "multimedia"*} -->
			{if !empty($objToAss.uri)}
				{$beEmbedMedia->object($objToAss,$params)}
			{/if}
			</td>
			<td style="white-space:nowrap; text-align:center">{$objToAss.modified|date_format:$conf->datePattern}</td>
			<td style="text-align:center">{$objToAss.lang}</td>
			<td style="text-align:center">{$objToAss.id}</td>
			<td><a class="BEbutton golink" style="padding:0px 2px 0px 2px !important; margin:0px" title="{$objToAss.nickname}" target="_blank" href="{$html->url('/')}view/{$objToAss.nickname}"></a></td>
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