<script type="text/javascript">
<!--
{literal}

function localRemoveItem(itemId) {
	$(itemId).remove();
	$(".itemBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[contents]["+index+"][id]") ;
		$(".priority", this).attr("name", "data[contents]["+index+"][priority]") ;
		$(".priority", this).attr("value", index+1) ;
	}) ;
}

$(document).ready(function(){
	$("#updateform").validate(); 
	$('div.tabsContainer > ul').tabs();
	$('div.tabsContainer > ul > li > a').changeActiveTabs();
	{/literal}{foreach key=index item=obj from=$contents|default:$empty name=contentsfe}{literal}
		$('#m_{/literal}{$obj.id}{literal}').setupDragDrop();
	{/literal}{/foreach}{literal}
});
{/literal}
//-->
</script>

<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">

{include file="../common_inc/form_title_subtitle.tpl"}

{include file="../common_inc/form_properties.tpl" publication=false}

{include file="../common_inc/form_tree.tpl" excludedSubTreeId=$section.id inputTreeType="radio" parents=$parent_id tpl_title="Where the section is"}


<div class="tab"><h2>{t}Contents for the section{/t}</h2></div>	

<fieldset id="sectionContentsContainer">
	
	
	
	<div id="fragment-1">
		
		{assign var="newPriority" 	value=1}
		{assign var="index" 		value=0}
		{foreach key=index item=obj from=$contents|default:$empty name=contentsfe}
		<div id="m_{$obj.id}" class="itemBox" style="width:85px;heigth:150px">
			<input type="hidden" class="index" 	name="index" value="{$index}" />
			<input type="hidden" class="id" 	name="data[contents][{$index}][id]" value="{$obj.id}" />
			<input type="text" class="priority" name="data[contents][{$index}][priority]" value="{$obj.priority|default:$smarty.foreach.contentsfe.iteration}" size="3" maxlength="3"/>
			<span class="label">{$conf->objectTypeModels[$obj.object_type_id]}</span>
			<br/>
			<table>
			<tr><td>{$obj.id}</td><td rowspan="2"><img src="{$html->webroot}img/beobject/{$conf->objectTypeModels[$obj.object_type_id]}.png" border="0" alt="{$conf->objectTypeModels[$obj.object_type_id]}"/></td></tr>
			<tr><td><img src="{$html->webroot}img/flags/{$obj.lang}.png" border="0" alt="{$obj.lang}"/></td></tr>
			<tr><td colspan="2"><b>{$obj.title|escape:'htmlall'}</b></td></tr>
			</table>
			<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
			<input type="button" onclick="localRemoveItem('#m_{$obj.id}');" value="{t}Delete{/t}"/>
			</div>
		</div>
		{foreachelse}
			{t}Empty{/t}
		{/foreach}
		
		<script type="text/javascript">
		<!--
		index = {$index} ;
		priority = {$newPriority} ;
		//-->
		</script>
	</div>
</fieldset>


{include file="../common_inc/form_custom_properties.tpl" el=$section}

{include file="../common_inc/form_permissions.tpl" el=$section recursion=true}

</form>
