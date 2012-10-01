<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var urls = Array();
urls['deleteSelected'] = "{$this->Html->url('deleteSelected/')}";
urls['changestatusSelected'] = "{$this->Html->url('changeStatusObjects/')}";
urls['copyItemsSelectedToAreaSection'] = "{$this->Html->url('addItemsToAreaSection/')}";
urls['moveItemsSelectedToAreaSection'] = "{$this->Html->url('moveItemsToAreaSection/')}";
urls['removeFromAreaSection'] = "{$this->Html->url('removeItemsFromAreaSection/')}";
var no_items_checked_msg = "{t}No items selected{/t}";

function count_check_selected() {
	var checked = 0;
	$('input[type=checkbox].objectCheck').each(function(){
		if($(this).attr("checked")) {
			checked++;
		}
	});
	return checked;
}
$(document).ready(function(){
	
	$("#deleteSelected").click(function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		if(!confirm(message)) 
			return false ;
		$("#formObject").attr("action", urls['deleteSelected']) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		var op = ($('#areaSectionAssocOp').val()) ? $('#areaSectionAssocOp').val() : "copy";
		$("#formObject").attr("action", urls[op + 'ItemsSelectedToAreaSection']) ;
		$("#formObject").submit() ;
	});

	$(".opButton").click( function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		$("#formObject").attr("action",urls[this.id]) ;
		$("#formObject").submit() ;
	});
});

//-->
</script>	

<style>
	.vlist { display:none }
</style>


<form method="post" action="" id="formObject">

	<div id="viewthumb">
	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th colspan="2" nowrap>
				{* 
				<img class="multimediaitemToolbar viewlist" src="{$this->Html->webroot}img/iconML-list.png" />
				<img class="multimediaitemToolbar viewsmall" src="{$this->Html->webroot}img/iconML-small.png" />
				<img class="multimediaitemToolbar viewthumb" src="{$this->Html->webroot}img/iconML-thumb.png" />
				*}
				 {t}order by{/t}:
			</th>
			<th>{$this->BeToolbar->order('id','id')}</th>
			<th>{$this->BeToolbar->order('title','Title')}</th>
			<th>{$this->BeToolbar->order('name','Name')}</th>
			<th>{$this->BeToolbar->order('mediatype','type')}</th>
			<th>{t}size{/t}</th>
			<th>{$this->BeToolbar->order('status','Status')}</th>
			<th>{$this->BeToolbar->order('modified','modified')}</th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}

	</table>

	<br style="clear:both" />
	{strip}	
		{foreach from=$objects item="item"}
			<div class="multimediaitem itemBox{if $item.status != "on"} off{/if}">
				{assign_associative var="params" item=$item}
				{$view->element('file_item',$params)}
				
				<table border=0 padding="0" spacing="0" style="width:100%">
					<tr>
					<td colspan=2 class="vlist">{$item.id}</td>
					<td colspan=2 class="vlist"><a href="{$this->Html->url('view/')}{$item.id}">{$item.title}</a></td>
					<td colspan=2 class="vlist">{$item.name}</td>
					<td colspan=2 class="vlist">{$item.mediatype}</td>
					<td colspan=2 class="vlist">{math equation="x/y" x=$item.file_size|default:0 y=1024 format="%d"|default:""} KB</td>
					<td colspan=2 class="vlist">{$item.status}</td>
					<td colspan=2 class="vlist">{$item.created|date_format:'%b %e, %Y'}</td>
					{if (empty($item.fixed))}	
					<td style="text-align:left;">
					<input type="checkbox" style="width:15px" name="objects_selected[]" class="objectCheck" title="{$item.id}" value="{$item.id}" />
					</td>
					{/if}			
					<td style="text-align:right;"><a href="{$this->Html->url('view/')}{$item.id}" class="BEbutton">+</a></td>
				</tr>	
				</table>
				
			</div>
		{/foreach}
	</div>
	
	{/strip}
	<br style="margin:0px; line-height:0px; clear:both" />


{if !empty($objects)}

<div style="border-top: 1px solid gray; padding-top:10px; margin-top:10px; white-space:nowrap">
	
	{t}Go to page{/t}: {$this->BeToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$this->BeToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div class="htabcontent" style="width:620px">

{t}change status to{/t}: 	<select style="width:75px" id="newStatus" name="newStatus">
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton" />
	<hr />

	{if !empty($tree)}

		{assign var='named_arr' value=$view->params.named}
		{if empty($named_arr.id)}
			{t}copy{/t}
		{else}
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> {t}copy{/t} </option>
				<option value="move"> {t}move{/t} </option>
			</select>
		{/if}
		&nbsp;{t}to{/t}:  &nbsp;

		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
		{$this->BeTree->option($tree)}
		</select>

		<input type="hidden" name="data[source]" value="{$named_arr.id|default:''}" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />

		{if !empty($named_arr)}
		<input id="removeFromAreaSection" type="button" value="{t}Remove selected from section{/t}" class="opButton" />
		<hr/>
		{/if}
	{/if}

	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>


