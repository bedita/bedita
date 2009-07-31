<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$html->url('addItemsToAreaSection/')}";


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
			<th>User</th>
			<th>Group</th>
			<th>IP</th>
			<th>completed on</th>
			<th>correct %</th>
			<th>checked</th>
			<th></th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{foreach from=$objects item="o" name="fclist"}
		<tr>
			<td class="checklist">
				<input type="checkbox" name="objects_selected[]" class="objectCheck"  />
			</td>
			<td><a href="{$html->url('view_session_results')}/{$o.id}">{$o.UserCreated.realname}</a></td>
			<td>Liceo Primo Levi, Bologna</td>
			<td>{$o.ip_created}</td>
			<td>aggiungere data di completamento</td>
			<td style="text-align:center">85%</td>
			<td><img src="{$html->webroot}img/iconNotes.gif" alt="notes" /></td>
		</tr>
		
		
		
		{foreachelse}
		
			<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
		
		{/foreach}
		
{if ($smarty.foreach.fclist.total) >= 10}
		
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

{t}change status to:{/t} 	<select style="width:75px" id="newStatus" name="newStatus">
								<option value=""> -- </option>
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />
	
	{if !empty($tree)}
			

			
			<select style="width:75px">
				<option> {t}copy{/t} </option>
				<option> {t}move{/t} </option>
			</select>
			
			  &nbsp;to:  &nbsp;
			
			<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			{$beTree->option($tree)}
			</select>
			
			<input id="assocObjects" type="button" value=" ok " />
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
	
	



