{*
** form form template
*}


   <div class="tab"><h2>{t}Question detail{/t}</h2></div>

		<fieldset id="question">
		
			
		<table class="bordered">
		
			<tr>
				<th>{t}title{/t}:</th>
				<td>
					<input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject" />
				</td>
			</tr>
		
			<tr>
				<th>{t}text{/t}:</th>
				<td>
					<textarea id="subtitle" style="width:380px; height:80px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
				</td>
			</tr>
			
			<tr>
				<th>{t}type{/t}:</th>
				<td>
					<select name="domanda[tipoID]" id="tipoID">
						<option label="1 - scelta multipla" value="1" selected="selected">scelta multipla</option>
						<option label="2 - scelta singola" value="2">scelta singola checkbox</option>
						<option label="10 - scelta singola a tendina" value="10">scelta singola a tendina</option>
						<option label="3 - testo libero" value="3">risposta a testo libero</option>
						<option label="4 - checkOpen" value="4">checkOpen</option>
						<option label="5 - grado" value="5">grado</option>
						<option label="9 - numero" value="9">numero</option>
					</select>
				</td>
			</tr>

			<tr>
				<th>{t}answers{/t}:</th>
				<td>
					
					<table class="answers">
					{section name="w" loop=5}
						<tr>
							<td style="padding-left:0px;">{$smarty.section.w.iteration}.&nbsp;</td>
							<td><textarea style="height:16px; width:220px !important;" name="" class="autogrowarea"></textarea></td>
							<td>
								<input type="button" title="{t}add{/t}" value="+" />
								<input type="button" title="{t}remove{/t}" value="-" />
							</td>
							<td>
								&nbsp;<input type="checkbox">&nbsp;{t}correct{/t}
							</td>
						</tr>
					{/section}
					</table>
					
				</td>
			</tr>
				
			<tr>
		
				<th>{t}status{/t}:</th>
				<td colspan="4">
					{if $object.fixed}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[fixed]" value="1" />
						<input type="hidden" name="data[status]" value="{$object.status}" />
					{else}
						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
					{/if}
				</td>
		
			</tr>
		
		
			<tr>
				<th>{t}author{/t}:</th>
				<td>
					<input type="text" name="data[creator]" value="{$object.creator}" />
				</td>
			</tr>
		
		
			<tr>
				<th>{t}language{/t}:</th>
				<td>
				{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
				<select name="data[lang]" id="main_lang">
					{foreach key=val item=label from=$conf->langOptions name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
					{foreach key=val item=label from=$conf->langsIso name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
				</select>
				</td>
			</tr>
		
		</table>
	
	</fieldset>

	{include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}

	{include file="../common_inc/form_tags.tpl"}
		
	{include file="../common_inc/form_translations.tpl"}

	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	

	
