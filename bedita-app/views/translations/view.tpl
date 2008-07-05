{*
** translations view template
*}


</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>I see trees of green, red roses too...</h1>
	translation of
	<h1 style="margin-top:0px">Vedo alberi verdi e rose rosse..</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="mainfull">	
	
	{include file="inc/form.tpl"}
		
</div>


{*include file="../common_inc/menuright.tpl"*}