<script type="text/javascript">
<!--
{if $object.fixed}
$(document).ready(function(){
		$("#whereto input[type=checkbox]").prop("disabled", true);
});
{/if}
//-->
</script>

<div class="tab"><h2>{t}{if empty($tpl_title)}Position{else}{$tpl_title}{/if}{/t}</h2></div>

<fieldset id="whereto">
	{if $object.fixed}{t}The content is fixed: it's not possible to change the position in the tree{/t}{/if}
	
	{if empty($tree)}
		{t}No tree found{/t}
	{else}

		<div class="publishingtree">
		
			{assign_associative var="params" checkbox=true}
			{$view->element('tree', $params)}
		
		</div>
		
	{/if}
	
</fieldset>

