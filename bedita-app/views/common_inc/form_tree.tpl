<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	
	{/literal}
	{if $object.status == 'fixed'}
		$("#whereto input[@type=checkbox]").attr("disabled","disabled");
	{/if}
	{literal}
	
});
{/literal}
//-->
</script>

	
<div class="tab"><h2>{t}{if empty($tpl_title)}Where put the document into{else}{$tpl_title}{/if}{/t}</h2></div>

<fieldset id="whereto">
	{if $object.status == 'fixed'}{t}The content is fixed: it's not possible to change the position in the tree{/t}{/if}
	
	{if empty($tree)}
		{t}No tree found{/t}
	{else}

		<div class="publishingtree" style="width:auto; margin-left:10px;">
		{$beTree->view($tree, "checkbox", $parents)}
		</div>
		
	{/if}
	
</fieldset>

