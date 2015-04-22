{$html->css('tree', null, ['inline' => false])}
{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}

<script type="text/javascript">
$(document).ready( function ()
{
	openAtStart("#tagdetails");

	$("#delBEObject").submitConfirm({
		action: "{$html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the tag?{/t}"
	});
});

</script>

{$view->element('modulesmenu')}

{include file = './inc/menuleft.tpl'}

<div class="head">

	<h2>
		{if $tag}
			Tag	“<em style="color:#FFFFFF; line-height:2em">{$tag.label|escape}</em>”
		{else}
			{t}New tag{/t}
		{/if}
	</h2>
	
</div>

<form action="{$html->url('/tags/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

</form>

{$view->element('menuright')}