{assign var='method' value=$method|default:'index'}

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
		$("#updateForm").attr("action","{/literal}{$html->url('/multimedia/cloneObject')}{literal}");
		var cloneTitle=prompt("{/literal}{t}Title{/t}{literal}",$("input[@name='data[title]']").val()+"-copy");
		$("input[@name='data[title]']").attr("value",cloneTitle);
		$("#updateForm").submit();
	});
});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/multimedia')}">{t}{$moduleName}{/t}</label>
	</div> 
	
	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if !empty($method) && $method != "index" && $module_modify eq '1'}
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
	
	{if $method == "index"}
	<div class="insidecol">
		<label>{t}filter by{/t}:</label>
		<ul>
			<li><input type="checkbox" name="filtertype[images]" />images</li>
			<li><input type="checkbox" name="filtertype[images]" />videos</li>
			<li><input type="checkbox" name="filtertype[images]" />texts</li>
			<li><input type="checkbox" name="filtertype[images]" />datasheets</li>
			<li><input type="checkbox" name="filtertype[images]" />applications</li>
			<li><input type="checkbox" name="filtertype[images]" />view all</li>
		</ul>
	</div>
	{/if}



</div>