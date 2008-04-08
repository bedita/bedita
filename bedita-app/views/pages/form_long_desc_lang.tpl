{assign var=object_lang value=$object.lang|default:$conf->defaultLang}

{if ($conf->mce|default:false)}
	{$javascript->link("tiny_mce/tiny_mce")}
	<script language="javascript" type="text/javascript">
		{literal}
		tinyMCE.init({
			mode : "textareas",
			editor_selector : "mce",
			theme : "simple",
			convert_urls : false 
		});
		$(document).ready(function(){
		{/literal}
		{foreach key=val item=label from=$conf->langOptions name=langfe}
		{if $val!=$object_lang && empty($object.LangText.abstract[$val])}
			{literal}$('#long_desc_langs_container > ul').tabs("disable",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
		{elseif $val==$object_lang}
			{literal}$('#long_desc_langs_container > ul').tabs("select",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
		{/if}
		{if $val!=$object_lang && empty($object.LangText.body[$val])}
			{literal}$('#long_desc_langs_container > ul').tabs("disable",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
		{elseif $val==$object_lang}
			{literal}$('#long_desc_langs_container > ul').tabs("select",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
		{/if}
		{/foreach}
		{literal}});{/literal}
	</script>
{/if}
</script>
<h2 class="showHideBlockButton">{t}Long Text{/t}</h2>
<div class="blockForm" id="extendedtext" style="display: none">

<fieldset>
	<div id="long_desc_langs_container" class="tabsContainer">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#long_desc_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="long_desc_lang_{$val}">
			<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/></h3>
			<b>{t}Short text{/t}:</b>
			<br/>
			<textarea name="data[LangText][{$val}][abstract]" id="text_{$val}" class="mce" style="font-size:13px; width:510px; height:150px;">{$object.LangText.abstract[$val]|default:''}</textarea>
			<br/>
			<b>{t}Long text{/t}:</b>
			<br/>
			<textarea name="data[LangText][{$val}][body]" id="text_l_{$val}" class="mce" style="font-size:13px; width:510px; height:150px;">{$object.LangText.body[$val]|default:''}</textarea>
		</div>
		{/foreach}
	</div>
</fieldset>
</div>