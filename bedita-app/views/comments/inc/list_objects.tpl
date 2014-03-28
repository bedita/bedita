<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteSelected/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$html->url('addItemsToAreaSection/')}";

{literal}
$(document).ready(function(){

	// avoid to perform double click
	$("a:first", ".indexlist .obj").click(function(e){ 
		e.preventDefault();
	});

	$(".indexlist .obj TD").not(".checklist").css("cursor","pointer").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	
	$("#deleteSelected").bind("click", function() {
		if (!confirm(message)) {
			return false;
		}
		$("#formObject").prop("action", urlDelete);
		$("#formObject").submit() ;
	});
	
	
	$("#assocObjects").click( function() {
		$("#formObject").prop("action", urlAddToAreaSection) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").prop("action", urlChangeStatus) ;
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
			<th>{$beToolbar->order('ReferenceObject.title','object title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created','inserted on')}</th>
			<th>{$beToolbar->order('email','email')}</th>
			<th>{$beToolbar->order('ip_created', 'IP')}</th>	
			<th>{$beToolbar->order('id','id')}</th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].ReferenceObject.title}</td>
			<td>{$objects[i].status}</td>
			<td>{$objects[i].created|date_format:$conf->dateTimePattern}</td>
			<td>{$objects[i].email|default:''}</td>
			<td>{$objects[i].ip_created}</td>
			<td>{$objects[i].id}</td>
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
	
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>

{t}change status to:{/t} 	<select style="width:75px" id="newStatus" name="newStatus">
								<option value=""> -- </option>
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />

	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>

<br />
<br />
<br />
<br />
	
	



