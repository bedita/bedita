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
	
	$("div.insidecol input[@name='clone']").click(function() {
		$("#updateForm").attr("action","{/literal}{$html->url('/addressbook/cloneObject')}{literal}");
		var cloneTitle=prompt("{/literal}{t}Title{/t}{literal}",$("input[@name='data[title]']").val()+"-copy");
		if (cloneTitle) {
			$("input[@name='data[title]']").attr("value",cloneTitle);
			$("#updateForm").submit();
		}
	});
});
{/literal}
</script>

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/addressbook')}">{t}{$moduleName}{/t}</label>
	</div> 
	

	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if $method == "view" && $module_modify eq '1'}
	
	<div class="insidecol">
		{if ($perms->isWritable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
		{/if}
		{if ($perms->isDeletable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />
		{/if}
	</div>
	
	{elseif $method == "index"}
	

		{if !empty($categories)}
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
				<ul id="groups" style="display:none">
					{foreach key=val item=cat from=$categories}
					<li><a href="{$html->url('/addressbook/index/category:')}{$cat.id}">{$cat.label}</a></li>
					{/foreach}
				</ul>
		</ul>
		{/if}
	{/if}



</div>

