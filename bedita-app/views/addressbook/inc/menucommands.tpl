{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	{if !empty($view->action) && $view->action != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if $view->action == "view" && $module_modify eq '1'}
	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		var cloneButton = $("div.insidecol input[name='clone']");
		cloneButton.unbind("click");
		cloneButton.click(function() {
			var company = $('input:radio[name*=company]:checked').val();
			if (company == 0) {
				var cloneTitle=prompt("{/literal}{t}name{/t},{t}surname{/t}{literal}",
						$("input[name='data[person][name]']").val() + "," +
						$("input[name='data[person][surname]']").val() +"-copy");
				if (cloneTitle) {
					var nameArr =  cloneTitle.split(",");
					$("input[name='data[person][name]']").attr("value",nameArr[0]);
					$("input[name='data[person][surname]']").attr("value",nameArr[1]);
					$("#updateForm").attr("action","{/literal}{$html->url('/')}{literal}addressbook/cloneObject");
					$("#updateForm").submit();
				}
			} else {
				var cloneTitle=prompt("{/literal}{t}name{/t}{literal}", $("input[name='data[cmp][company_name]']").val() +"-copy");
				if (cloneTitle) {
					$("input[name='data[cmp][company_name]']").attr("value",cloneTitle);
					$("#updateForm").attr("action","{/literal}{$html->url('/')}{literal}addressbook/cloneObject");
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
	
	{$view->element('prevnext')}
	
	{elseif $view->action == "index"}

	{$view->element('select_categories')}

	{/if}



</div>

