

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$html->url('addItemsToAreaSection/')}";
var urlCheckMulti = "{$html->url('checkMultiUrl/')}";

{literal}
$(document).ready(function(){


	$(".indexlist TD").not(".checklist").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	/* select/unselect each item's checkbox */
	$(".selectAll").bind("click", function(e) {
		var status = this.checked;
		$(".objectCheck").each(function() { this.checked = status; });
	}) ;
	/* select/unselect main checkbox if all item's checkboxes are checked */
	$(".objectCheck").bind("click", function(e) {
		var status = true;
		$(".objectCheck").each(function() { if (!this.checked) return status = false;});
		$(".selectAll").each(function() { this.checked = status;});
	}) ;
	
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
	
	$("#checkSelected").bind("click", function() {
		$("#formObject").attr("action", urlCheckMulti) ;
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
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('url', 'Url')}</th>
			<th>{t}Link{/t}</th>
			<th>{$beToolbar->order('http_code', 'Check result')}</th>
			<th>{$beToolbar->order('http_response_date', 'Last check')}</th>
			<th style="text-align:center">{$beToolbar->order('status', 'Status')}</th>
			<th>Notes</th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td class="checklist">
			{if (empty($objects[i].fixed))}
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{/if}
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].url|default:''}</td>
			<td><a href="{$objects[i].url|default:''}" target="_blank">[{t}visit this url{/t}]</a></td>
			<td>{$objects[i].http_code|default:''}</td>
			<td>{$objects[i].http_response_date|date_format:$conf->dateTimePattern|default:''}</td>
			<td style="text-align:center">{$objects[i].status}</td>
			<td>{if $objects[i].note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
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
	
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>

	<div>
		{t}check urls{/t}: <input id="checkSelected" type="button" value="{t}check selected links{/t}" />
		<hr />
		
		{t}change status to:{/t}
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value=""> -- </option>
			{html_options options=$conf->statusOptions}
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
		
		<hr />
		
		<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
		
		<hr />
		
		Export to:
		<select name="export">
			<option>Delicious(XBEL)</option>
			<option>Excel</option>
		</select>
	
	</li>
</ul>
	</div>

{/if}

</form>



<br />
<br />
<br />
<br />
	
	



