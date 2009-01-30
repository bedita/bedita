{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		{literal}
	});
	
	$("div.insidecol input[@name='save']").click(function() {
		$("#updateForm").submit();
	});
});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   	   <label class="{$moduleName}" rel="{$html->url('/')}{$session->read("backFromView")}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	
	{include file="../common_inc/messages.tpl"}
	
	
	{if !empty($method) && $method != "index" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />	
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />

	</div>
	
	{if $session->read("prevNext") && !empty($object.id)}
		{assign var="prevNext" value=$session->read("prevNext")}
		<div class="listobjnav">
			{if $prevNext[$object.id].prev}
			<a title="prev" href="{$html->url('/')}{$currentModule.path}/view/{$prevNext[$object.id].prev}">
				‹
			</a>
			{/if}
	
			{if $prevNext[$object.id].next}
			<a title="next" href="{$html->url('/')}{$currentModule.path}/view/{$prevNext[$object.id].next}">
				›
			</a> 
			{/if}
		</div>
	{/if}
	
	{/if}



</div>

