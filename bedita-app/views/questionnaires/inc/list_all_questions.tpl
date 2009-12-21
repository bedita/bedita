

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteQuestion/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index_questions/')}" ;
var urlChangeStatus = "{$html->url('changeStatusQuestions/')}";

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
		$("#formObject").attr("action", urlAddToAreaSection) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").attr("action", urlChangeStatus) ;
		$("#formObject").submit() ;
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
			<th>{$beToolbar->order('title','Title')}</th>
			<th>{$beToolbar->order('question_type','type')}</th>
			<th>{$beToolbar->order('status','Status')}</th>
			<th>{$beToolbar->order('modified','modified')}</th>
			<th>{$beToolbar->order('note','notes')}</th>
			<th></th>
		</tr>
	{/capture}
	
	{$smarty.capture.theader}
	
	{section name="i" loop=$objects}
	<tr class="obj {$objects[i].status}">
		<td class="checklist" style="padding-top:5px; vertical-align:top">
			<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}"/>
		</td>
		<td>
			<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a>
			<div style="display:none; color:gray; font-style:italic" class="listitemdesc">{$objects[i].description|nl2br}</div>
		</td>
		<td>
			{assign var="qtype" value=$objects[i].question_type}
			{t}{$conf->questionTypes[$qtype]}{/t}
		</td>
		<td style="text-align:center">{$objects[i].status|upper}</td>
		<td>{$objects[i].modified|date_format:'%d-%m-%y'}</td>
		<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
	</tr>
	
	{sectionelse}
	
		<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
	
	{/section}
	
	{if ($smarty.section.i.total) >= 10}
			
		{$smarty.capture.theader}
				
	{/if}


</table>


	{*section name="i" loop=$objects}

	<div class="questionbox{if $objects[i].status != 'on'} off {/if}">
		
		<input style="vertical-align:top;" 
		type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
		&nbsp; 
		{if $objects[i].note|default:''}<img style="margin:-2px;" src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}
		
		<h2 style="margin:2px 0px 5px 0px;">
			<a style="color:#8C3540" href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:36:'~':true|default:"<i>[no title]</i>"}</a>
		</h2>
		<label>tipo</label>: scelta multipla
		<br />
		<label>status</label>: {$objects[i].status|upper}
		&nbsp;
		<label>lastmod</label>: {$objects[i].modified|date_format:'%d-%m-%y'}	
	</div>
			
	{sectionelse}
		
			<p style="margin:10px">{t}No questions found{/t}</p>
		
	{/section*}

<br style="clear:both" />
<hr />

	
{if !empty($objects)}

<div style="white-space:nowrap">	
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
	<div>
		{t}change status to:{/t}
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value=""> -- </option>
			{html_options options=$conf->statusOptions}
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
		<hr />
		Create a new questionnaire with selected elements &nbsp;&nbsp; | &nbsp;&nbsp; title: <input type="text" />  <input type="button" value=" create " />
		<hr />
		Apply tags: <input type="text" />  <input type="button" value=" ok " />
		<hr />
		<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	</div>

{/if}

</form>


