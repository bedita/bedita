{$javascript->link("jquery/jquery.changealert", false)}

</head>

<body>
	
{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="mailgroups"}

<div class="head">
	
	<h1>{t}Lists{/t}</h1>

</div>

{include file="inc/menucommands.tpl" method="mailgroups"}

<div class="mainfull">
	
{include file="inc/list_mail_groups.tpl" method="mailgroups"}

</div>