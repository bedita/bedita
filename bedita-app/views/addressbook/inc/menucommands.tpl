{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if $method == "view" && $module_modify eq '1'}
	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		var cloneButton = $("div.insidecol input[@name='clone']");
		cloneButton.unbind("click");
		cloneButton.click(function() {
			$("#updateForm").attr("action","{/literal}{$html->url('/')}{literal}addressbook/cloneObject");
			var company = $('input:radio[name*=company]:checked').val();
			if (company == 0) {
				var cloneTitle=prompt("{/literal}{t}name{/t},{t}surname{/t}{literal}",
						$("input[@name='data[person][name]']").val() + "," +
						$("input[@name='data[person][surname]']").val() +"-copy");
				if (cloneTitle) {
					var nameArr =  cloneTitle.split(",");
					$("input[@name='data[person][name]']").attr("value",nameArr[0]);
					$("input[@name='data[person][surname]']").attr("value",nameArr[1]);
					$("#updateForm").submit();
				}
			} else {
				var cloneTitle=prompt("{/literal}{t}name{/t}{literal}", $("input[@name='data[cmp][company_name]']").val() +"-copy");
				if (cloneTitle) {
					$("input[@name='data[cmp][company_name]']").attr("value",cloneTitle);
					$("#updateForm").submit();
				}
			}
		});
	});
	{/literal}
	</script>
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
	</div>
	
	{include file="../common_inc/prevnext.tpl"}
	
	{elseif $method == "index"}
	

		{if !empty($categories)}
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
				
				<ul id="groups" {if (empty($categorySearched))}style="display:none"{/if}>
					{foreach key=val item=cat from=$categories}
					<li {if (($categorySearched|default:'')==$cat.id)}class="on"{/if}><a href="{$html->url('/addressbook/index/category:')}{$cat.id}">{$cat.label}</a></li>
					{/foreach}
				</ul>
		</ul>
		{/if}
	{/if}



</div>

