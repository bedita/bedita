{$html->css('module.superadmin')}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.pwdstrengthmeter")}

</head>

<body>

{include file="modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="viewUser"}

<div class="head">
	
	<h2>
		{if isset($user)}
			{t}User settings{/t}<br />
			“<em style="color:#FFFFFF; line-height:2em">{$user.User.realname}</em>”
		{else}
			{t}New user{/t}
		{/if}
	</h2>

</div>

<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm" class="cmxform">

{include file="inc/menucommands.tpl" method="viewUser" fixed=true}

<div class="main">
	
	{include file="inc/form_user.tpl" method="viewUser"}
	
</div>

</form>

{include file="menuright.tpl"}
