
<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteSelected/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$html->url('addItemsToAreaSection/')}";
var urlMoveToAreaSection = "{$html->url('moveItemsToAreaSection/')}";
var urlCategoryAssoc = "{$html->url('assocCategory/')}";
var urlCategoryDisassoc = "{$html->url('disassocCategory/')}";
var urlMailgroupAssoc = "{$html->url('addToMailgroup/')}";

{literal}
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	$("#deleteSelected").bind("click", function() {
		if(!confirm(message)) 
			return false ;	
		$("#formObject").attr("action", urlDelete) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		var url = urlAddToAreaSection;
		if($('#areaSectionAssocOp')) {
			op = $('#areaSectionAssocOp').val()
			if(op == 'move') {
				url = urlMoveToAreaSection;
			}
		}
		$("#formObject").attr("action", url) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").attr("action", urlChangeStatus) ;
		$("#formObject").submit() ;
	});

	$("#assocObjectsCategory").click( function() {
		$("#formObject").attr("action", urlCategoryAssoc) ;
		$("#formObject").submit() ;
	});

	$("#disassocObjectsCategory").click( function() {
		$("#formObject").attr("action", urlCategoryDisassoc) ;
		$("#formObject").submit() ;
	});

	$("#assocObjectsMailgroup").click( function() {
		var mailgroup = $('#objMailgroupAssoc').val();
		if(mailgroup != '') {
			$("#formObject").attr("action", urlMailgroupAssoc) ;
			$("#formObject").submit() ;
		}
	});
});


{/literal}

//-->
</script>	
	
	<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>

	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th></th>
			<th>{$beToolbar->order('title','name')}&nbsp;&nbsp;&nbsp;&nbsp;{$beToolbar->order('surname','surname')}</th>
{*			<th>{$beToolbar->order('company_name','organization')}</th>*}
<th>{$beToolbar->order('id','id')}</th>
			<th>{$beToolbar->order('status','Status')}</th>
			<th>{$beToolbar->order('modified','modified')}</th>
			<th>{t}is user{/t}</th>
			<th>{$beToolbar->order('email','email')}</th>
			<th>{$beToolbar->order('country','country')}</th>
			<th>{$beToolbar->order('note','Notes')}</th>	
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td class="checklist" style="padding-top:5px">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}"/>
			</td>

			<td style="min-width:200px">
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
		
{*			<td>{$objects[i].company_name|default:''}</td>*}
			<td style="text-align:center">{$objects[i].status}</td>
			<td>{$objects[i].modified|date_format:$conf->dateTimePattern}</td>
			<td style="text-align:center">{if empty($objects[i].user_id)}{t}no{/t}{else}{t}yes{/t}{/if}</td>
			<td>{$objects[i].email|default:''}</td>
			<td>{$objects[i].country}</td>
			<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
		</tr>
		
		
		
		{sectionelse}
		
			<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
		
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
	{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span>
	| &nbsp;&nbsp;
	{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span>	
	
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>

{t}change status to:{/t} 	<select style="width:75px" id="newStatus" name="newStatus">
								<option value=""> -- </option>
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />
	
	{if !empty($tree)}
		{assign var='named_arr' value=$view->params.named}
		{if empty($named_arr)}
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

		<input type="hidden" name="data[source]" value="{$named_arr.id|default:''}" />
		<input id="assocObjects" type="button" value=" ok " />
	<hr />
	{/if}

	{if !empty($categories)}
		{t}category{/t}
		<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
		<option value="">--</option>
		{foreach from=$categories item='category' key='key'}
		<option value="{$key}">{$category}</option>
		{/foreach}
		</select>
		<input id="assocObjectsCategory" type="button" value="{t}Add association{/t}" /> / <input id="disassocObjectsCategory" type="button" value="{t}Remove association{/t}" />
		<hr />
	{/if}

	{if !empty($mailgroups)}
		{t}mailgroups{/t}
		<select id="objMailgroupAssoc" name="data[mailgroup]">
		<option value="">--</option>
		{foreach from=$mailgroups item='mailgroup' key='key'}
		<option value="{$mailgroup.id}">{$mailgroup.group_name}</option>
		{/foreach}
		</select>
		<input id="assocObjectsMailgroup" type="button" value="{t}Add association{/t}" /> / <input id="disassocObjectsMailgroup" type="button" value="{t}Remove association{/t}" />
		<hr />
	{/if}

	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>

<br />
<br />
<br />
<br />
	
	



