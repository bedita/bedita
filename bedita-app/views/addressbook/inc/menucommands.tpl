{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	{if !empty($view->action) && $view->action != "index"}
		{assign var="back" value=$session->read("backFromView")|escape}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{assign var="user" value=$session->read('BEAuthUser')}
	
	{if $view->action == "view" && $module_modify eq '1'}
		<script type="text/javascript">
		$(document).ready(function() {
			var cloneButton = $("div.insidecol input[name='clone']");
			cloneButton.unbind("click");
			cloneButton.click(function() {
				var company = $('input:radio[name*=company]:checked').val();
				if (company == 0) {
					var cloneTitle = prompt("{t}name{/t},{t}surname{/t}",
							$("input[name='data[person][name]']").val() + "," +
							$("input[name='data[person][surname]']").val() + "-copy");
					if (cloneTitle) {
						var nameArr =  cloneTitle.split(",");
						$("input[name='data[person][name]']").val(nameArr[0]);
						$("input[name='data[person][surname]']").val(nameArr[1]);
						$("#updateForm").prop("action", "{$html->url('/')}{$moduleName}/cloneObject");
						$("#updateForm").submit();
					}
				} else {
					var cloneTitle = prompt("{t}name{/t}", $("input[name='data[cmp][company_name]']").val() + "-copy");
					if (cloneTitle) {
						$("input[name='data[cmp][company_name]']").val(cloneTitle);
						$("#updateForm").prop("action", "{$html->url('/')}{$moduleName}/cloneObject");
						$("#updateForm").submit();
					}
				}
			});
		});
		</script>
		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
			<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
		</div>
		
		{$view->element('prevnext')}
	
	{/if}
</div>

