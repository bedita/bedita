<script type="text/javascript">
	var testSmtpUrl = "{$html->url('/admin/testSmtp')}";

	function delElems(elem) { 
		var prev = $(elem).prev("input");
		$(elem).prev("input").remove();
		$(elem).remove();
	} 

	$(document).ready(function() { 
		$("#system_config").prev(".tab").BEtabstoggle();
		$("#general_config").prev(".tab").BEtabstoggle();
		$('#addLocale').click(function () { 
			var v = $('#localesV').val();
			if($('input[value="' + v + '"]').length == 0) { 
				var key = $('#localesK').val();
				var value = $('#localesV').val();
				var newinput = '<input type="text" name="sys[locales][' + key + ']" value="' + value + '" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />'
				$('#localesAdded').append(newinput);
			} 
		} );
		$('#addTranslationLang').click(function () { 
			var label = $('#translationLangs option:selected').text();
			if(!($('input[title="' + label + '"]')) || ($('input[title="' + label + '"]').length == 0)) { 
				var value = $('#translationLangs').val();
				var index = $('#translationLangs').prop("selectedIndex");
				var newinput = '<input type="text" rel="' + index + '" title="' + value + '" name="cfg[langOptions][' + value + ']" value="' + label + '" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />'
				$('#translationLangsAdded').append(newinput);
			} 
		} );
		$('#translationLangsTr').hide();
		$("#backendExtraLangs").click(function () { 
			var nextDiv = $(this).next("div");
			nextDiv.show();
		} );
		$("#allLangsY").click(function () { 
			$("#translationLangsTr").hide();
		} );
		$("#allLangsN").click(function () { 
			$("#translationLangsTr").show();
		} );
		{if !$conf->langOptionsIso}$("#translationLangsTr").show();{/if}
		$("#testSmtp").click(function() { 
			to = prompt("{t}Send email to{/t}");
			if (to) {
				$("#configForm").prop("action", testSmtpUrl + "/" + to);
				$("#configForm").submit();
			}
		} );
	} );
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewConfig"}

{include file="inc/menucommands.tpl" method="viewConfig" fixed=true}

<div class="mainfull">

	<form action="{$html->url('/admin/saveConfig')}" method="post" name="configForm" id="configForm">
		{$beForm->csrf()}
		<div class="tab"><h2>{t}System configuration{/t}</h2></div>

		<fieldset id="system_config">

			{if !empty($bedita_cfg_err)}
				<p>{$bedita_cfg_err}</p>
			{else}
			<table border=0 style="margin-bottom:10px">

				<tr>
					<th style="text-transform:none">{t}BEdita url{/t}:</th>
					<td>
						<input type="text" name="sys[beditaUrl]" value="{$conf->beditaUrl|escape}" style="width: 480px;"/>
					</td>
					{if !empty($bedita_url_err)}
					<td>
						{$bedita_url_err}
					</td>
					{/if}
				</tr>
				<tr>
					<th>{t}Media root{/t}:</th>
					<td>
						<input type="text" name="sys[mediaRoot]" value="{$conf->mediaRoot|escape}" style="width: 480px;"/>
					</td>
					{if !empty($media_root_err)}
					<td>
						{$media_root_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Media url{/t}:</th>
					<td>
						<input type="text" name="sys[mediaUrl]" value="{$conf->mediaUrl|escape}" style="width: 480px;"/>
					</td>
					{if !empty($media_url_err)}
					<td>
						{$media_url_err}
					</td>
					{/if}
				</tr>
				
				</table>
				
				
{* <!--
		<table>		
			<tr>
					<th>{t}Date Pattern{/t}:</th>
					<td>
						<input type="text" name="sys[datePattern]" value="{$conf->datePattern}" style="width: 300px;"/>
					</td>
					{if !empty($date_pattern_err)}
					<td>
						{$date_pattern_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Date Time Pattern{/t}:</th>
					<td>
						<input type="text" name="sys[dateTimePattern]" value="{$conf->dateTimePattern}" style="width: 300px;"/>
					</td>
					{if !empty($date_time_pattern_err)}
					<td>
						{$date_time_pattern_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Locales{/t}:</th>
					<td>
						{t}key{/t}: <input type="text" id="localesK" />
						{t}value{/t}: <input type="text" id="localesV" />
						<input type="button" value="{t}Add{/t}" id="addLocale"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div id="localesAdded" style="width:180px;">
						{if !empty($conf->locales)}
						{foreach $conf->locales as $langKey => $langLabel name='lof'}
						<input type="text" title="{$langLabel}" name="sys[locales][{$langKey}]" value="{$langLabel}" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />
						{/foreach}
						{/if}
						</div>
					</td>
				</tr>
		</table>	
 -->
*}
				

			{/if}


		</fieldset>

		<div class="tab"><h2>{t}General configuration{/t}</h2></div>

		<fieldset id="general_config">

			{if !empty($bedita_cfg_err)}
				<p>{$bedita_cfg_err}</p>
			{else}
			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th>{t}Project name{/t}:</th>
					<td>
						<input type="text" name="cfg[projectName]" value="{$conf->projectName|escape}" style="width: 300px;"/>
					</td>
				</tr>
                <tr>
                    <th>{t}Colophon widget{/t}:</th>
                    <td>
                    	{strip}
                        <textarea name="cfg[colophon]" 
                            style="font-size:0.8em; color:gray; width:470px;">
                            {$conf->colophon|default:''|escape}
                        </textarea>
                        {/strip}
                    </td>
                </tr>
			</table>
			{/if}
		</fieldset>
			
		<div class="tab"><h2>{t}Language configuration{/t}</h2></div>

		<fieldset id="general_config">

			{if !empty($bedita_cfg_err)}
				<p>{$bedita_cfg_err}</p>
			{else}
			<table class="bordered">
				<tr>
					<th>{t}Backend languages{/t}:</th>
					<td>
						{if !empty($po_langs)}
						{foreach $po_langs as $langKey name='lof'}
						<input name="sys[langsSystem][{$langKey}]" type="checkbox" {foreach key=key item=item name=l from=$conf->langsSystem}{if $key == $langKey} checked="checked"{/if}{/foreach} value="{$conf->langOptionsDefault[$langKey]}" />
							{$conf->langOptionsDefault[$langKey]}
						{/foreach}
						{/if}
					</td>
				</tr>

				<tr>
					<th>{t}User Interface default language{/t}:</th>
					<td>
						<select name="cfg[defaultUILang]">
							{foreach $conf->langsSystem as $langKey => $langLabel}
							<option value="{$langKey}"{if $langKey == $conf->defaultUILang} selected{/if}>{$langLabel}</option>
							{/foreach}
						</select>
					</td>
				</tr>

				<tr>
					<th>{t}Use all available languages for contents{/t}:</th>
					<td>
						<input id="allLangsY" name="cfg[langOptionsIso]" type="radio" value="true" {if $conf->langOptionsIso}checked="checked"{/if} />{t}Yes{/t}
						<input id="allLangsN" name="cfg[langOptionsIso]" type="radio" value="false" {if !$conf->langOptionsIso}checked="checked"{/if} />{t}No, use custom list{/t}
					</td>
				</tr>
				<tr id="translationLangsTr">
					<th>{t}Select content languages{/t}:</th>
					<td>
						<select id="translationLangs">
							<option></option>
							{foreach $langs_iso as $langKey => $langLabel}
							<option value="{$langKey}">{$langLabel}</option>
							{/foreach}
						</select>
						<input type="button" value="{t}Add{/t}" id="addTranslationLang"/>
						<br/>
						<div id="translationLangsAdded" style="width:200px;">
						{foreach from=$conf->langOptions key="langKey" item="langLabel" name="lof"}
						<input type="text" rel="{$smarty.foreach.lof.index}" title="{$langLabel}" name="cfg[langOptions][{$langKey}]" value="{$langLabel}" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />
						{/foreach}
						</div>
					</td>
				</tr>
				
				<tr>
					<th>{t}New contents default language{/t}:</th>
					<td>
						<select name="cfg[defaultLang]">
							{foreach $conf->langOptions as $langKey => $langLabel}
							<option value="{$langKey}" {if $langKey == $conf->defaultLang}selected="selected"{/if}>{$langLabel}</option>
							{/foreach}
							{foreach $langs_iso as $langKey => $langLabel}
							<option value="{$langKey}" {if $langKey == $conf->defaultLang}selected="selected"{/if}>{$langLabel}</option>
							{/foreach}
						</select>
					</td>
				</tr>
			</table>
			{/if}
		</fieldset>
		
		<div class="tab"><h2>{t}Mail configuration{/t}</h2></div>
		<fieldset>
			<table class="bordered">
				<tr>
					<th rowspan=4 style="padding-top:10px; vertical-align:top"><b>{t}Smtp Options{/t}</b>:</th>
					<th>{t}port{/t}:</th>
					<td><input type="text" name="sys[smtpOptions][port]" value="{$conf->smtpOptions.port|default:''|escape}" /></td>
					<th>{t}timeout{/t}:</th>
					<td><input type="text" name="sys[smtpOptions][timeout]" value="{$conf->smtpOptions.timeout|default:''|escape}" /></td>
				</tr>
				<tr>
					<th>{t}host{/t}:</th>
					<td colspan="3"><input type="text" name="sys[smtpOptions][host]" value="{$conf->smtpOptions.host|default:''|escape}" /></td>
				</tr>
				<tr>
					<th>{t}username{/t}:</th>
					<td><input type="text" name="sys[smtpOptions][username]" value="{$conf->smtpOptions.username|default:''|escape}" autocomplete="off"/></td>
					<th>{t}password{/t}:</th>
					<td><input type="password" name="sys[smtpOptions][password]" autocomplete="off"/></td>
				</tr>
				<tr>
					<td colspan="4">
						
							<input type="button" style="width:50%" id="testSmtp" value="  test smtp  " /> 
						
					</td>
				</tr>
				<tr>
					<th rowspan=2 style="padding-top:10px; vertical-align:top"><b>{t}Mail support{/t}</b>:</th>
					<th>{t}from{/t}:</th>
					<td><input type="text" name="sys[mailSupport][from]" value="{$conf->mailSupport.from|default:''|escape}" /></td>
					<th>{t}to{/t}:</th>
					<td><input type="text" name="sys[mailSupport][to]" value="{$conf->mailSupport.to|default:''|escape}" /></td>
				</tr>
				<tr>
					<th>{t}subject{/t}:</th>
					<td><input type="text" name="sys[mailSupport][subject]" value="{$conf->mailSupport.subject|default:''|escape}" /></td>
				</tr>
			</table>
		</fieldset>

	</form>

</div>