{$html->css('module.superadmin')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

<script type="text/javascript">
<!--
{literal}
	function viewUser(objectid) {
		document.location = "{/literal}{$html->url('/admin/viewUser')}{literal}/" + objectid;
	}
	function delUserDialog(userid,objectid) {
		if(!confirm("{/literal}{t}Do you really want to remove user{/t}{literal} " + userid + "?")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('/admin/removeUser')}{literal}/" + objectid;
	}
{/literal}
//-->
</script>

</head>

<body>

{include file="modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl"}

{include file="toolbar.tpl"}

<div class="mainfull">

	{include file="inc/list_users.tpl" method="index"}
	
</div>
