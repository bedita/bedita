{*
Template Events.
*}
{php}
$vs = &$this->get_template_vars() ;
//pr($vs["html"]->params);
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
		{include file="_incl_menu.tpl" sez="indice" firstContent=$Events.items[0]}
		</td>	
		<td>
		{* BEGIN -- Main Content *}
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

		{$bevalidation->formTag('frmModifyAreas', null, './edit', 'post')}
		<table border="0" cellspacing="0" cellpadding="2" class="indexList">
		
		<tr>
			<th>tipo</th>
			<th>nome</th>
			<th>lingua</th>
			<th>status</th>
			<th>server name</th>
		</tr>
		{section name=i loop=$Aree}
			{assign var="index" 		value=$smarty.section.i.index}
			{assign var="id" 			value=$Aree[i].Area.id}
			{assign var="name" 			value=$Aree[i].Area.name}
			{assign var="lang" 			value=$Aree[i].Area.lang}
			{assign var="tipo" 			value=$Aree[i].Area.tipo}
			{assign var="servername" 	value=$Aree[i].Area.servername}
			{assign var="status" 		value=$Aree[i].Area.status}

			<tr>
				<td>tipo</td>
				<td>
				<input type="text" id="data_$index_AreaName" name="data[$index][Area][name]" value="{$Aree[i].Area.name}">
				{bevalidationHelper fnc="rule" args="'frmModifyAreas', 'data_$index_AreaName:name|required'"}
				</td>
				<td>lingua</td>
				<td>status</td>
				<td>
				<input type="text" id="data_$index_AreaServername" name="data[$index][Area][servername]" value="{$Aree[i].Area.servername}">
				</td>
			</tr>
		{/section}
		</table>
		
{assign var="back" 		value=$html->here}
{assign var="id" 		value=$User.User.id}
{assign var="status"	value=$User.User.status}


{htmlHelper fnc="hidden" args="'back/ERROR', array('value' => '$back')"}
{htmlHelper fnc="hidden" args="'User/id', array('value' => '$id')"}
{htmlHelper fnc="hidden" args="'User/status', array('value' => '$status')"}

</form>
		
		{* END -- Main Content *}
		</td>	
	</tr>
</table>

