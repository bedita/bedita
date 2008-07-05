

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{*include file="../common_inc/toolbar.tpl"*}



<div class="mainfull">
	<h2>TODO</h2>
	qui un  list di tutti gli objects che possidono una traduzione 
	finita o meno
	, e la relativa toolbar 
	<br>
	-- stringhe da decommentare quando il controller è pronto --
	<br>
	<code>
		include file="../common_inc/toolbar.tpl"
		<br>
		include file="../common_inc/list_objects.tpl" method="index"
	</code>
	<br>
	anche le operazioni di massa sono le stesse di tutti gli object...
	cambia status ed eliminazione (no tree)
	<br />
	il link al dettaglio è <a href="{$html->url('/translations/view/')}">QUESTO</a>
	<br />
	<br />

	{*include file="../common_inc/list_objects.tpl" method="index"*}
	
</div>
