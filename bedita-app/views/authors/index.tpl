{*
Template Authors.
*}
{php}
$vs = &$this->get_template_vars() ;
//pr($vs["Authors"]["toolbar"]);
//exit;
{/php}
</head>
<body>
	<div id="header">
		{include file="head.tpl"}
	</div>

<table border="0" cellspacing="0" cellpadding="0" class="mainTable">
	<tr>
		<td>
		{* Comandi a SX  *}	
		{include file="_incl_menu.tpl" sez="indice" firstContent=$Authors.items[0]}
		</td>	
		<td>
		{* BEGIN -- Main Content *}
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

		{include file="toolbarList.tpl" sez="menuCentro" toolbar=$Authors.toolbar}
		
		<div class="gest_menuLeft" style="float:left;">
		{include file="areeGruppiTree.tpl" Groups=$Subjects}
		</div>
		
		{include file="contentsList.tpl" Lists=$Authors}
		
		<br><br>
		<input type="button" onClick="document.location ='./frmAdd'" value="aggiungi un nuovo autore" style="margin:10px;">
		
		{include file="toolbarList.tpl" sez="menuCentro" toolbar=$Authors.toolbar}
		
		{* END -- Main Content *}
		</td>	
	</tr>
</table>

