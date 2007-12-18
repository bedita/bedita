{$html->css('module.superadmin')}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.treeview")}
<script type="text/javascript">
<!--
{literal}
function viewGroup(objectid) {
	document.location = "{/literal}{$html->url('/admin/viewGroup')}{literal}/" + objectid;
}
function delGroupDialog(name,objectid) {
	if(!confirm("{/literal}{t}Do you really want to remove group{/t}{literal} " + name + "?")) {
		return false ;
	}
	document.location = "{/literal}{$html->url('/admin/removeGroup')}{literal}/" + objectid;
}
{/literal}
//-->
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
	{include file="submenu.tpl" method="groups"}
	{include file="form_groups.tpl" method="groups"}
</div>