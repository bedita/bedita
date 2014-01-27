<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var urls = Array();
urls['deleteSelected'] = "{$html->url('deleteSelected/')}";
urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
urls['copyItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
urls['moveItemsSelectedToAreaSection'] = "{$html->url('moveItemsToAreaSection/')}";
urls['removeFromAreaSection'] = "{$html->url('removeItemsFromAreaSection/')}";
urls['assocObjectsCategory'] = "{$html->url('assocCategory/')}";
urls['disassocObjectsCategory'] = "{$html->url('disassocCategory/')}";
var no_items_checked_msg = "{t}No items selected{/t}";
var sel_status_msg = "{t}Select a status{/t}";
var sel_category_msg = "{t}Select a category{/t}";
var sel_copy_to_msg = "{t}Select a destination to 'copy to'{/t}";

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

	// avoid to perform double click
	$("a:first", ".indexlist .obj").click(function(e){ 
		e.preventDefault();
	});

	$(".indexlist .obj TD").not(".checklist").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	$("#deleteSelected").bind("click", function() {
		if(count_check_selected()<1) {
			alert(no_items_checked_msg);
			return false;
		}
		if(!confirm(messageSelected)) 
			return false ;	
		$("#formObject").attr("action", urls['deleteSelected']) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		if(count_check_selected()==0) {
			alert(no_items_checked_msg);
			return false;
		}
		if($('#areaSectionAssoc').val() == "") {
			alert(sel_copy_to_msg);
			return false;
		}
		var op = ($('#areaSectionAssocOp').val()) ? $('#areaSectionAssocOp').val() : "copy";
		$("#formObject").attr("action", urls[op + 'ItemsSelectedToAreaSection']) ;
		$("#formObject").submit() ;
	});

	$(".opButton").click( function() {
		if(count_check_selected()==0) {
			alert(no_items_checked_msg);
			return false;
		}
		if(this.id.indexOf('changestatus') > -1) {
			if($('#newStatus').val() == "") {
				alert(sel_status_msg);
				return false;
			}
		}
		if(this.id == 'assocObjectsCategory') {
			if($('#objCategoryAssoc').val() == "") {
				alert(sel_category_msg);
				return false;
			}
		}
		if(this.id == 'disassocObjectsCategory') {
			$('#objCategoryAssoc').attr('value',$('#filter_category').val());
		}
		$("#formObject").attr("action",urls[this.id]) ;
		$("#formObject").submit() ;
	});
});

//-->
</script>	
	
<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>

	<table class="indexlist">
	{capture name="theader"}
		<thead>
		<tr>
			<th>{$beToolbar->order('fixed', '&nbsp;')}</th>
			<th>{$beToolbar->order('title', 'title')}</th>
			<th style="text-align:center">{$beToolbar->order('id', 'id')}</th>
			<th style="text-align:center">{$beToolbar->order('status', 'status')}</th>
			<th>{$beToolbar->order('modified', 'modified')}</th>
			<th style="text-align:center">
				{assign_associative var="htmlAttributes" alt="comments" border="0"} 
				{$beToolbar->order('num_of_comment', '', 'iconComments.gif', $htmlAttributes)}
			</th>			
			<th>{$beToolbar->order('lang', 'lang')}</th>
			<th>{$beToolbar->order('num_of_editor_note', 'notes')}</th>
		</tr>
		</thead>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td class="checklist">
			{if !empty($objects[i].start_date) && ($objects[i].start_date|date_format:"%Y%m%d") > ($smarty.now|date_format:"%Y%m%d")}
			
				<img title="{t}object scheduled in the future{/t}" src="{$html->webroot}img/iconFuture.png" style="height:28px; vertical-align:top;">
			
			{elseif !empty($objects[i].end_date) && ($objects[i].end_date|date_format:"%Y%m%d") < ($smarty.now|date_format:"%Y%m%d")}
			
				<img title="{t}object expired{/t}" src="{$html->webroot}img/iconPast.png" style="height:28px; vertical-align:top;">
			
			{elseif (!empty($objects[i].start_date) && (($objects[i].start_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d"))) or ( !empty($objects[i].end_date) && (($objects[i].end_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d")))}
			
				<img title="{t}object scheduled today{/t}" src="{$html->webroot}img/iconToday.png" style="height:28px; vertical-align:top;">

			{/if}
			
			{if !empty($objects[i].num_of_permission)}
				<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; margin:0 0 0 -5px; vertical-align:top;">
			{/if}
			
			{if ($objects[i].ubiquity|default:0 > 1)}
				<img title="{t}ubiquous object{/t}" src="{$html->webroot}img/iconUbiquity.png" style="margin:4px 4px 0 0; height:18px; vertical-align:top;">
			{/if}

			{if (empty($objects[i].fixed))}
				<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{else}
				<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
			{/if}


			</td>
			<td style="min-width:300px">
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a>
				<div class="description" id="desc_{$objects[i].id}">
					nickname:{$objects[i].nickname}<br />
					{$objects[i].description}
				</div>
			</td>
			<td class="checklist detail" style="text-align:left; padding-top:4px;">
				<a href="javascript:void(0)" onclick="$('#desc_{$objects[i].id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span>			
				&nbsp;
				{$objects[i].id}
				</a>	
			</td>
			<td style="text-align:center">{$objects[i].status}</td>
			<td>{$objects[i].modified|date_format:$conf->dateTimePattern}</td>
			<td style="text-align:center">{$objects[i].num_of_comment|default:0}</td>
			<td>{$objects[i].lang}</td>
			<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
		</tr>
		
		{sectionelse}
		
			<tr><td colspan="100" style="padding:30px">{t}No items found{/t}</td></tr>
		
		{/section}
		
{if ($smarty.section.i.total) >= 10}
		
	{$smarty.capture.theader}
			
{/if}

</table>

<br />
	
{if !empty($objects)}

<div style="white-space:nowrap">
	
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>
	&nbsp;&nbsp;&nbsp
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;
	{t}of{/t}&nbsp;
	{if ($beToolbar->pages()) > 0}
	{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
	{else}1{/if}
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	
	&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;
	{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span>	
	| &nbsp;&nbsp;
	{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span>
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t}&nbsp;<span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>

{t}change status to{/t}: 	<select style="width:75px" id="newStatus" name="newStatus">
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton"/>
	<hr />
	
	{if !empty($tree)}
		{assign var='named_arr' value=$view->params.named}
		{if empty($sectionSel.id)}
			{t}copy{/t}
		{else}
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> {t}copy{/t} </option>
				<option value="move"> {t}move{/t} </option>
			</select>
		{/if}
		&nbsp;{t}to{/t}:  &nbsp;

		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
		{$beTree->option($tree)}
		</select>

		<input type="hidden" name="data[source]" value="{$sectionSel.id|default:''}" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />
		
		{if !empty($sectionSel.id)}
			{assign var='filter_section_id' value=$sectionSel.id}
			{assign var='filter_section_name' value=$pubSel.title|default:$sectionSel.title}
			<input id="removeFromAreaSection" type="button" value="{t}Remove selected from{/t} '{$filter_section_name}'" class="opButton" />
			<hr/>
		{/if}
	{/if}

	{if !empty($categories)}
		{t}category{/t}
		<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
		<option value="">--</option>
		{foreach from=$categories item='category' key='key'}
		{if !empty($named_arr.category) && ($key == $named_arr.category)}{assign var='filter_category_name' value=$category}{/if}
		<option value="{$key}">{$category}</option>
		{/foreach}
		</select>
		<input id="assocObjectsCategory" type="button" value="{t}Add association{/t}" class="opButton"/>
		<hr />
		{if !empty($named_arr.category)}
			{assign var='filter_category_id' value=$named_arr.category}
			<input id="disassocObjectsCategory" type="button" value="{t}Remove selected from category{/t} '{$filter_category_name}'" class="opButton" />
			<input id="filter_category" type="hidden" name="filter_category" value="{$filter_category_id}" />
			<hr />
		{/if}
	{/if}
	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</div>

{/if}

</form>

<br />
<br />
<br />
<br />