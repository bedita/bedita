{$javascript->link("jquery/jquery.changealert", false)}

</head>

<body>
	
{include file="../common_inc/modulesmenu.tpl" method="categories"}

{include file="inc/menuleft.tpl" method="categories"}

<div class="head">
	
	<h1>{t}Categories{/t}</h1>

</div>

{include file="inc/menucommands.tpl" method="categories" method="index"}


<div class="main">
{include file="../pages/list_categories.tpl" method="index"}
</div>


