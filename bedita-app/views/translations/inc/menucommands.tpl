{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
{if !empty($object_master)}
{assign_concat var=back_url 0="/" 1=$object_master.ObjectType.module 2="/view/" 3=$object_master.id}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("#delLangText").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		{literal}
	});

	$("div.insidecol input[@name='save']").click(function() {
		$("#updateForm").submit();
	});
	
	var urlBack = '{/literal}{$html->url("$back_url")}{literal}';
	$("#backBEObject").click(function() {
		document.location = urlBack;
	});
});
</script>
{/literal}
{/if}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		<label class="{$moduleName}" rel="{$html->url('/translations')}">{t}{$moduleName}{/t}</label>
	</div>

	{assign var="user" value=$session->read('BEAuthUser')}

	{if !empty($method) && $method != "index" && $module_modify|default:'' eq '1'}
	<div class="insidecol">
		
		{if ($perms->isWritable($user.userid,$user.groups,$object_master.Permissions))}
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />
		{/if}
		{if ($perms->isDeletable($user.userid,$user.groups,$object_master.Permissions))}
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delLangText" {if !($object_translation.id|default:false)}disabled="1"{/if} />
		{/if}
		{if !empty($object_master)}
		<input class="bemaincommands" type="button" value="{t}Back to {$object_master.ObjectType.name}{/t}" name="back" id="backBEObject"/>
		{/if}
	</div>
	{/if}

</div>
