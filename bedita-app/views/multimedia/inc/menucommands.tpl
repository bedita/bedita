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
		if (cloneTitle) {
			$("input[@name='data[title]']").attr("value",cloneTitle);
			$("#updateForm").submit();
		}
	});
});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/multimedia')}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if !empty($method) && $method != "index" && $module_modify eq '1'}
	<div class="insidecol">
		{if ($perms->isWritable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
		{/if}
		{if ($perms->isDeletable($user.userid,$user.groups,$object.Permissions))}
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		{/if}
	</div>
	
	{/if}

	{assign var='cat' value=$categorySearched|default:''}

	{if $method == "index"}
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#mediatypes').slideToggle();">{t}Select by type{/t}</a></li>
				<ul id="mediatypes" style="display:none">
					
					{foreach from=$conf->mediaTypes item="media_type"}
					<li class="ico_{$media_type} {if $cat==$media_type}on{/if}">
						<a href="{$html->url('/multimedia')}/index/category:{$media_type}">
						{$media_type}
						</a>
					</li>
					{/foreach}
					<li class="ico_all">
						<a href="{$html->url('/multimedia')}">All</a>
					</li>
				
				</ul>
		</ul>
	{/if}	



</div>