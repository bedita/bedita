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
	   <label class="{$moduleName}" rel="{$html->url('/newsletter')}">{t}{$moduleName}{/t}</label>
	</div> 
	

	{assign var="user" value=$session->read('BEAuthUser')}

	{if $method eq "subscribers"}	
	
	
	{elseif $method eq "groups"}
	
	
	{elseif $method eq "newsletters"}
	
	
	{elseif !empty($method) && $method != "index" && $module_modify eq '1'}
	
	<div class="insidecol">
		{if ($perms->isWritable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
		{/if}
		{if ($perms->isDeletable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />
		{/if}
	</div>
	
	{/if}


	

</div>

