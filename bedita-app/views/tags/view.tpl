{assign_associative var="cssOptions" inline=false}
{$html->css('tree', null, $cssOptions)}
{$html->script("form", false)}
{$html->script("jquery/jquery.changealert", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}


<script type="text/javascript">
{literal}
$(document).ready( function ()
{
	openAtStart("#tagdetails");

	$("#delBEObject").submitConfirm({
		action: "{/literal}{$html->url('delete/')}{literal}",
		message: "{/literal}{t}Are you sure that you want to delete the tag?{/t}{literal}"
	});
});

{/literal}
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

<form action="{$html->url('/tags/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

</form>

{$view->element('menuright')}