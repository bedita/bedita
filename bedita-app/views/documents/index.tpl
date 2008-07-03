

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../common_inc/toolbar.tpl"}



<div class="mainfull">

	{include file="../common_inc/list_objects.tpl" method="index"}
	
	
	
	

{*
<pre>			
{$beToolbar->current()}
{$beToolbar->size()}
{$beToolbar->pages()}
{$beToolbar->first()} 
{$beToolbar->prev()}  
{$beToolbar->next()} 
{$beToolbar->last()}
*}
{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} | {t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;</li>


</div>


