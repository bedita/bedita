{*
Template Events.
*}
{php}
$vs = &$this->get_template_vars() ;
//pr($vs["Tipologies"]);
//exit;
{/php}

</head>
<body>
	<div id="header">
		{include file="head.tpl"}
	</div>

<table border="1" cellspacing="0" cellpadding="0" class="mainTable">
	<tr>
		<td>
		{* Comandi a SX  *}	
		{include file="_incl_menu.tpl" sez="indice"}
		</td>	
		<td>
		{* BEGIN -- Main Content *}
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

		{include file="toolbarList.tpl" sez="menuCentro" toolbar=$Events.toolbar}
		
		<div class="gest_menuLeft" style="float:left;">
		{include file="areeGruppiTree.tpl" Groups=$Tipologies}
		</div>
		
		<table border="0" cellspacing="0" cellpadding="2" class="indexList">
		
		</table>
		
		<br><br>
		
		{include file="toolbarList.tpl" sez="menuCentro" toolbar=$Events.toolbar}
		
		{* END -- Main Content *}
		</td>	
	</tr>
</table>

