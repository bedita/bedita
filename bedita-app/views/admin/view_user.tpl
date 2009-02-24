{$html->css('module.superadmin')}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		openAtStart("#details");
	});
</script>
{/literal}

{include file="../common_inc/form_common_js.tpl"}

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

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

<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm" class="cmxform">

{include file="inc/menucommands.tpl" method="viewUser" fixed=true}

<div class="main">
	
	{include file="inc/form_user.tpl" method="viewUser"}

</div>

{include file="../common_inc/menuright.tpl"}

</form>


