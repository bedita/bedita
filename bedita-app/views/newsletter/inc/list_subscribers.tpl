

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
	
	$("select").change(function() {
		location.href = $(this).val();
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
			<th>{$paginator->sort('Email', 'email')}</th>
			<th>{$paginator->sort('ID', 'id')}</th>
			<th>{$paginator->sort('in addressbook', 'Card.name')}</th>
			<th>{$paginator->sort('Status', 'status')}</th>
			<th>{$paginator->sort('html', 'html')}</th>
			<th>{$paginator->sort('data', 'created')}</th>
			
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{foreach from=$subscribers item="sub" name="f"}
	
		<tr class="obj on">
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$sub.MailAddress.id}" value="{$sub.MailAddress.id}" />
			</td>
			<td><a href="{$html->url('viewsubscriber/')}{$sub.MailAddress.id}">{$sub.MailAddress.email}</a></td>
			<td>{$sub.MailAddress.id}</td>
			<td>{$sub.Card.name|default:''} {$sub.Card.surname|default:''}</td>
			<td>{t}{$sub.MailAddress.status}{/t}</td>
			<td>{if $sub.MailAddress.html == 1}html{else}txt{/if}</td>
			<td>{$sub.MailAddress.created|date_format:$conf->datePattern}</td>
		</tr>
		
		{/foreach}

{if ($smarty.foreach.f.total) >= 10}
		
			{$smarty.capture.theader}
			
{/if}


</table>


<br />
	
{if !empty($sub)}

<div style="white-space:nowrap">
	
	{t}Go to page{/t}:
	<select name="pagSelectBottom" id="pagSelectBottom">
		
		{section name="i" start=0 loop=$html->params.paging.MailAddress.count step=1}
			{assign_associative var="options" page=$smarty.section.i.iteration}
			<option value="{$paginator->url($options)}">{$smarty.section.i.iteration}</option>
		{/section}
		
	</select>		 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}:
	<select id="selectTop" name="selectTop">
		{assign_associative var="options" limit="1"}
		<option value="{$paginator->url($options)}">1</option>
		{assign_associative var="options" limit="5"}
		<option value="{$paginator->url($options)}">5</option>
		{assign_associative var="options" limit="10"}
		<option value="{$paginator->url($options)}">10</option>
		{assign_associative var="options" limit="20"}
		<option value="{$paginator->url($options)}">20</option>
		{assign_associative var="options" limit="50"}
		<option value="{$paginator->url($options)}">50</option>
		{assign_associative var="options" limit="100"}
		<option value="{$paginator->url($options)}">100</option>
	</select>
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>

{t}change status to:{/t}
<select style="width:75px" id="newStatus" name="newStatus">
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
	
	



