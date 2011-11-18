{literal}
<script type="text/javascript">
	function delLang(elem) {
		var prev = $(elem).prev("input");
		$(elem).prev("input").remove();
		$(elem).remove();
	}

	$(document).ready(function(){
		$("#system_config").prev(".tab").BEtabstoggle();
		$("#general_config").prev(".tab").BEtabstoggle();

		$('#addTranslationLang').click(function () {
			var label = $('#translationLangs option:selected').text();
			if($('input[value="' + label + '"]').length == 0) {
				var value = $('#translationLangs').attr('value');
				var index = $('#translationLangs').attr("selectedIndex");
				var newinput = '<input type="text" rel="' + index + '" title="' + value + '" name="cfg[langOptions][' + value + ']" value="' + label + '" readonly="readonly" /><input type="button" value="-" onclick="delLang(this)" />'
				$('#translationLangsAdded').append(newinput);
			}
		});
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewConfig"}

{include file="inc/menucommands.tpl" method="viewConfig" fixed=true}

<div class="mainfull">


	<form action="{$html->url('/admin/saveConfig')}" method="post" name="configForm" id="configForm">


		<div class="tab"><h2>{t}System configuration{/t}</h2></div>

		<fieldset id="system_config">

			{if !empty($bedita_sys_err)}
				<p>{$bedita_sys_err}</p>
			{else}
			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th>{t}Media root{/t}:</th>
					<td>
						<input type="text" name="sys[mediaRoot]" value="{$conf->mediaRoot}" style="width: 300px;"/>
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
						<input type="text" name="sys[mediaUrl]" value="{$conf->mediaUrl}" style="width: 300px;"/>
					</td>
					{if !empty($media_url_err)}
					<td>
						{$media_url_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Bedita url{/t}:</th>
					<td>
						<input type="text" name="sys[beditaUrl]" value="{$conf->beditaUrl}" style="width: 300px;"/>
					</td>
					{if !empty($bedita_url_err)}
					<td>
						{$bedita_url_err}
					</td>
					{/if}
				</tr>

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

			</table>
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
						<input type="text" name="cfg[projectName]" value="{$conf->projectName}" style="width: 300px;"/>
					</td>
				</tr>
				
				<tr>
					<th>{t}User Interface default language{/t}:</th>
					<td>
						<select name="cfg[Config][language]">
							{foreach $conf->langsSystem as $langKey => $langLabel}
							<option value="{$langKey}"{if $langKey == $conf->Config.language} selected{/if}>{$langLabel}</option>
							{/foreach}
						</select>
					</td>
				</tr>

				<tr>
					<th>{t}Objects default language{/t}:</th>
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
				<tr>
					<th>{t}Translation languages{/t}:</th>
					<td>
						<select id="translationLangs">
							{foreach $langs_iso as $langKey => $langLabel}
							<option value="{$langKey}">{$langLabel}</option>
							{/foreach}
						</select>
						<input type="button" value="{t}Add{/t}" id="addTranslationLang"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="translationLangsAdded">
						{foreach $conf->langOptions as $langKey => $langLabel name='lof'}
						<input type="text" rel="{$smarty.foreach.lof.index}" title="{$langLabel}" name="cfg[langOptions][{$langKey}]" value="{$langLabel}" readonly="readonly" /><input type="button" value="-" onclick="delLang(this)" />
						{/foreach}
						</div>
					</td>
				</tr>
			</table>
			{/if}
		</fieldset>


	</form>

</div>