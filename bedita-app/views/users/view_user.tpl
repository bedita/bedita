{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}

<script type="text/javascript">
	$(document).ready(function() {
		openAtStart("#details");
	});
</script>


{$view->element('form_common_js')}

{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl" method="viewUser"}

<div class="head">
	
	<h1>
		{if !empty($userdetail)}
			{t}User{/t}	“<em style="color:#FFFFFF">{$userdetail.realname|default:$userdetail.userid|escape}</em>”
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


