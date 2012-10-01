{assign_associative var="cssOptions" inline=false}
{$this->Html->css('tree', null, $cssOptions)}
{$this->Html->script("form", false)}
{$this->Html->script("jquery/jquery.changealert", false)}
{$this->Html->script("jquery/jquery.form", false)}
{$this->Html->script("jquery/jquery.cmxforms", false)}
{$this->Html->script("jquery/jquery.metadata", false)}

<script type="text/javascript">
$(document).ready( function ()
{
	openAtStart("#tagdetails");

	$("#delBEObject").submitConfirm({
		action: "{$this->Html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the tag?{/t}"
	});
});

</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">

	<h2>
		{if $tag}
			Tag	“<em style="color:#FFFFFF; line-height:2em">{$tag.label}</em>”
		{else}
			{t}New tag{/t}
		{/if}
	</h2>
	
</div>

<form action="{$this->Html->url('/tags/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

</form>

{$view->element('menuright')}