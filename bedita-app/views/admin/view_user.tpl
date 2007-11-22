{$html->css('module.superadmin')}
{$javascript->link("interface")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.pwdstrengthmeter")}
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
	{include file="submenu.tpl" method="viewUser"}
	{include file="form_user.tpl" method="viewUser"}
</div>