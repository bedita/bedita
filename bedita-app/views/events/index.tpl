{*
Template Events.
*}

</head>
<body>
	<div id="header">
		{include file="head.tpl"}
	</div>

<table border="0" cellspacing="0" cellpadding="0" class="mainTable">
	<tr>
		<td>
		{* Comandi a SX  *}	
		<div class="gest_menuLeft">{include file="_incl_menu.tpl" sez="indice" firstContent=$Events.0|default:""}</div>
		</td>	
		<td>
		{* BEGIN -- Main Content *}
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

		{if !empty($paginator)}{include file="pagination.tpl" sez="menuCentro"}{/if}
		
		<div class="gest_menuLeft" style="float:left;">
		{include file="areeGruppiTree.tpl" Groups=$Tipologies}
		</div>
		
		{include file="contentsList.tpl" Lists=$Events}
		
		<br><br>
		<input type="button" onClick="document.location ='{$html->url('/events/frmAdd')}'" value="aggiungi nuovo evento" style="margin:10px;">
		
		{if !empty($paginator)}{include file="pagination.tpl" sez="menuCentro"}{/if}
		
		{* END -- Main Content *}
		</td>	
	</tr>
</table>

