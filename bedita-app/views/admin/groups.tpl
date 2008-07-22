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

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="groups"}

<div class="head">
	
	<h2>{t}Groups admin{/t}</h2>

</div>

<form action="{$html->url('/admin/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">

{include file="inc/menucommands.tpl" method="groups" fixed=true}

<div class="main">

	{include file="inc/form_groups.tpl"}

</div>

</form>