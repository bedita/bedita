<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	
	{/literal}
	{if $object.fixed}
		$("#whereto input[@type=checkbox]").attr("disabled","disabled");
	{/if}
	{literal}
	
});
{/literal}
//-->
</script>

	
<div class="tab"><h2>{t}{if empty($tpl_title)}Position{else}{$tpl_title}{/if}{/t}</h2></div>

<fieldset id="whereto">
	{if $object.fixed}{t}The content is fixed: it's not possible to change the position in the tree{/t}{/if}
	
	{if empty($tree)}
		{t}No tree found{/t}
	{else}

		<div class="publishingtree" style="width:auto; margin-left:10px;">
		{$beTree->view($tree, "checkbox", $parents)}
		</div>
		
	{/if}
	
</fieldset>

