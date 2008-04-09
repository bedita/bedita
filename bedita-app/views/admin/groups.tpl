{$html->css('module.superadmin')}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.treeview", false)}
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