{*
Template Bibliographies.
*}

</head>
<body>
	<div id="header">
		{include file="head.tpl"}
	</div>
{$html->link("label", "/biblio/index", "")}
<table border="0" cellspacing="0" cellpadding="0" class="mainTable">
	<tr>
		<td>
		{* Comandi a SX  *}	
		{include file="_incl_menu.tpl" sez="indice" firstContent=$Bibliographies.0|default:""}
		</td>	
		<td>
		{* BEGIN -- Main Content *}
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

		{if !empty($paginator)}{include file="pagination.tpl" sez="menuCentro"}{/if}
		
		<div class="gest_menuLeft" style="float:left;">
		{include file="areeGruppiTree.tpl" Groups=$Categories}
		</div>
		
		{include file="contentsList.tpl" Lists=$Bibliographies}
		
		<br><br>
		<input type="button" onClick="document.location ='{$html->url('/bibliographies/frmAdd')}'" value="aggiungi una nuova bibliografia" style="margin:10px;">
		
		{if !empty($paginator)}{include file="pagination.tpl" sez="menuCentro"}{/if}
		
		{* END -- Main Content *}
		</td>	
	</tr>
</table>

