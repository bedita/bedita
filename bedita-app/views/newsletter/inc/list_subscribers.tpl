

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusAddress/')}";
var urlAddToGroup = "{$html->url('addAddressToGroup/')}{$selected_group_id}";


{literal}
$(document).ready(function(){
	
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
		$("#formObject").attr("action", urlAddToGroup) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").attr("action", urlChangeStatus) ;
		$("#formObject").submit() ;
	});
	
	$("#selectPagContainer select").change(function() {
		location.href = $(this).val();
	});
	
});


{/literal}

//-->
</script>	


	{assign var="pagParams" value=$paginator->params()}
	
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
			<td>
			{if !empty($sub.Card)}
				{$sub.Card.name|default:''} {$sub.Card.surname|default:''}
			{elseif !empty($sub.MailAddress.Card)}
				{$sub.MailAddress.Card.name|default:''} {$sub.MailAddress.Card.surname|default:''}
			{/if}
			</td>
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

<div id="selectPagContainer" style="white-space:nowrap">
	
	{t}Go to page{/t}:
	<select name="pagSelectBottom" id="pagSelectBottom">
		
		{section name="i" start=0 loop=$pagParams.count step=1}
			{assign_associative var="options" page=$smarty.section.i.iteration}
			<option value="{$paginator->url($options)}"{if $pagParams.page == $options.page} selected{/if}>{$smarty.section.i.iteration}</option>
		{/section}
		
	</select>		 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}:
	<select id="selectTop" name="selectTop">
		{assign_associative var="options" limit="1"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 1} selected{/if}>1</option>
		{assign_associative var="options" limit="5"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 5} selected{/if}>5</option>
		{assign_associative var="options" limit="10"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 10} selected{/if}>10</option>
		{assign_associative var="options" limit="20"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 20} selected{/if}>20</option>
		{assign_associative var="options" limit="50"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 50} selected{/if}>50</option>
		{assign_associative var="options" limit="100"}
		<option value="{$paginator->url($options)}"{if $pagParams.options.limit == 100} selected{/if}>100</option>
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
		<option value="valid">{t}valid{/t}</option>
		<option value="blocked">{t}blocked{/t}</option>
	</select>
	
	<input id="changestatusSelected" type="button" value=" ok " />
	
	<hr />
	

	<select name="operation">
		{if $selected_group_id}<option value="move">{t}move{/t}</option>{/if}
		<option value="copy">{t}copy{/t}</option>
	</select>

	{t}to group:{/t} 	
	<select name="destination">
		<option value=""> -- </option>
		{if !empty($groups)}
		{foreach from=$groups item="group"}
			<option value="{$group.MailGroup.id}">{$group.MailGroup.group_name}</option>
		{/foreach}
		{/if}
	</select>

	<input id="assocObjects" type="button" value=" ok " />
	<hr />

	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>

<br />
<br />
<br />
<br />
	
	



