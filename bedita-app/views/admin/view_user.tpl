{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{*
{$javascript->link("jquery/jquery.validate", false)}
*}
{$javascript->link("jquery/jquery.changealert", false)}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		openAtStart("#details");
	});
</script>
{/literal}

{$view->element('form_common_js')}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewUser"}

<div class="head">
	
	<h1>
		{if !empty($userdetail)}
			{t}User{/t}	“<em style="color:#FFFFFF; line-height:2em">{$userdetail.realname}</em>”
		{else}
			{t}New user{/t}
		{/if}
	</h1>

</div>

{include file="inc/menucommands.tpl" method="viewUser" fixed=true}

<div class="main">
	
	{include file="inc/form_user.tpl" method="viewUser"}

</div>

{*$view->element('menuright')*}


