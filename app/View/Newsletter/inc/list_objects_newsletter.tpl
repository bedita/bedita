
<script type="text/javascript">
<!--
var urlDelete = "{$this->Html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$this->Html->url('index/')}" ;
var urlChangeStatus = "{$this->Html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$this->Html->url('addItemsToAreaSection/')}";

$(document).ready(function(){
	
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

//-->
</script>	


	<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>


	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th></th>
			<th>{$this->BeToolbar->order('title','Title')}</th>
			<th>{$this->BeToolbar->order('id','id')}</th>
			<th>{$this->BeToolbar->order('status','Status')}</th>
			<th>{$this->BeToolbar->order('sent','last invoice')}</th>
			<th>{$this->BeToolbar->order('template','Template')}</th>	
			<th>{$this->BeToolbar->order('lang','language')}</th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
		
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].mail_status}">
			<td style="width:15px;">
			{if (empty($objects[i].fixed))}
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{/if}
			</td>
			<td><a href="{$this->Html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].id}</td>

			{if !empty($objects[i].mail_status) && $objects[i].mail_status == "injob"}
				<td style="color:red; text-decoration: blink;">{t}in job{/t}</td>
			{elseif  ($objects[i].mail_status == "pending")}
				<td class="info">{t}{$objects[i].mail_status|default:''}{/t}</td>
			{else}
				<td>{t}{$objects[i].mail_status|default:''}{/t}</td>
			{/if}
					
			
			<td>{if !empty($objects[i].sent)}{$objects[i].sent|date_format:$conf->dateTimePattern} {/if}</td>
			
			<td>
			{if !empty($objects[i].relations.template)}
				<a href="{$this->Html->url('/newsletter/viewtemplate/')}{$objects[i].relations.template.0.id}">{$objects[i].relations.template.0.title}</a>
			{/if}
			</td>
			<td>{$objects[i].lang}</td>
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
	
	{t}Go to page{/t}: {$this->BeToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$this->BeToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>



{/if}

</form>

<br />
<br />
<br />
<br />
	
	



