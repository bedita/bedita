

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{*include file="../common_inc/toolbar.tpl"*}


<div class="mainfull">

	<table class="indexlist">	
	<tr>
		<th>{t}Object Id{/t}</th>
		<th>{t}Title{/t}</th>
		<th>{t}Lang{/t}</th>
		<th>{t}Original title{/t}</th>
		
		<th>{t}Status{/t}</th>
		<th>{t}Type{/t}</th>
	</tr>
	{foreach from=$translations item=i key=k}
	{assign var="ot" value=$objects_translated[$i.LangText.object_id][$i.LangText.lang].BEObject.object_type_id}
	<tr class="rowList" rel="{$html->url('/translations/view/')}{$i.LangText.object_id}/{$i.LangText.lang}">
		<td>{$i.LangText.object_id}</td>
		<td>{$translations_title[$i.LangText.object_id][$i.LangText.lang]}</td>
		<td>{$i.LangText.lang}</td>
		<td>XXXtitolo orignale</td>
		<td>{$i.LangText.text}</td>
		<td>{$conf->objectTypeModels[$ot]}</td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5">{t}No translation found{/t}</td>
	</tr>
	{/foreach}
	</table>

	

<br />
<div class="tab"><h2>{t}search and filter:{/t}</h2></div>
<fieldset id="filtering">
	
	<form action="{$html->url('/translations/index')}" method="post">
		<input type="hidden" name="data[id]" value="{$object_translation.id.status|default:''}"/>
		<input type="hidden" name="data[master_id]" value="{$object_master.id|default:''}"/>
	
	{t}Show translations in{/t}: &nbsp;
	<select style="font-size:1.2em;" name="data[translation_lang]">
		<option value="">{t}all languages{/t}</option>
	{foreach key=val item=label from=$conf->langOptions}
		<option value="{$val}">{$label}</option>
	{/foreach}
	</select>
	
	&nbsp;&nbsp;
	
	{t}with status{/t}:
	<select style="font-size:1.2em;" name="data[translation_status]">
		<option value="">{t}on,off,draft,required{/t}</option>
		<option value="on">{t}on{/t}</option>
		<option value="off">{t}off{/t}</option>
		<option value="draft">{t}draft{/t}</option>
		<option value="required">{t}todo{/t}</option>
	</select>
	
	<hr/>
	
	{t}Translations for object id{/t}:
	<input type="text" name="data[translation_object_id]"/>
	&nbsp;&nbsp;
	<input type="submit" value="{t}Refresh data{/t}"/>
	</form>

</fieldset>



	<div class="todo">
	<h2>TODO</h2>
	titolo originale
	<br />
	toolbar (la solita, next prev last etc)
	<br />
	le operazioni di massa da fare sono: cambio di status ed eliminazione
	<br />
	l'elenco delle lingue per una nuova translation dev'essere quello totale ISO e non $conf->langOptions<br />
	per il filtro di ricerca potrebbe essere usato invece quello delle lingue effettivamente presenti.<br />
	L'elenco delle lingue diponibili per l'interfaccia di BE invece (quello nel footer per intenderci)
	non c'entra niente con l'elenco ISO ma è solo l'elenco delle INTERfacce disponibili 
	che lascerei in <b>$conf->langOptions</b>.
	</div>
	
</div>
