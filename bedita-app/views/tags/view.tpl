{$html->css('tree')}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}

<script type="text/javascript">
{literal}
$(document).ready( function ()
{
	var openAtStart ="#tagdetails";
	$(openAtStart).prev(".tab").BEtabstoggle();
	$("#updateform").validate();
	$("#delBEObject").submitConfirm({
		action: "{/literal}{$html->url('delete/')}{literal}",
		message: "{/literal}{t}Are you sure that you want to delete the tag?{/t}{literal}"
	});
});

{/literal}
</script>
</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

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

<input type="hidden" name="data[id]" value="{$tag.id|default:''}"/>
<input type="hidden" name="tags_selected[0]" value="{$tag.id|default:''}"/>
<input type="hidden" name="data[name]" value="{$tag.name|default:''}"/>

{include file="inc/menucommands.tpl" method="view" fixed=true}




<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

</form>

