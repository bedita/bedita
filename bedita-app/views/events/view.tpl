{*
** event view template
*}


</head>
<body>
	
{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

{assign var=objIndex value=0}
	
	
{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="main">
		
	{include file="inc/form.tpl"}	

</div>


{include file="../common_inc/menuright.tpl"}