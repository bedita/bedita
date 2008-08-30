

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


	$("TABLE.indexList TD.cellList").click(function(i) { 
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
			<th>{$beToolbar->order('email', 'Email')}</th>
			<th>{$beToolbar->order('userid', 'userid')}</th>
			<th>{$beToolbar->order('addressbook', 'in addressbook')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('lang', 'Language')}</th>
			<th>{$beToolbar->order('html', 'html')}</th>
			<th>{$beToolbar->order('data', 'data')}</th>
			
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		<tr class="obj on">
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" {if $objects[i].status == 'fixed'}disabled="disabled"{/if} />
			</td>
			<td><a href="{$html->url('viewsubscriber/')}{$objects[i].id}">osama.bin@laden.com</a></td>
			<td>2</td>
			<td>Armando Callone</td>
			<td>on</td>
			<td>ita</td>
			<td>html</td>
			<td>21 gen 2007</td>
		</tr>
	{section name="i" loop=45}
		<tr class="obj on">
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" {if $objects[i].status == 'fixed'}disabled="disabled"{/if} />
			</td>
			<td><a href="{$html->url('viewsubscriber/')}{$objects[i].id}">cadendo.si.sfracella@dallamontagna.com</a></td>
			<td><em>none</em></td>
			<td><em>no</em></td>
			<td>on</td>
			<td>eng</td>
			<td>text</td>
			<td>12 apr 2008</td>
		</tr>
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
	

<select>
	<option>move</option>
	<option>copy</option>
</select>
&nbsp;
{t}to group:{/t} 	<select >
								<option value=""> -- </option>
								<option>elenco dei recipients groups</option>
							</select>
			<input type="button" value=" ok " />
	<hr />
	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>

<br />
<br />
<br />
<br />
	
	



