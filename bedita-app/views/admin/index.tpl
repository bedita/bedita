{$html->css('module.superadmin')}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("module.admin")}
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
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="list_users.tpl" method="index"}
</div>