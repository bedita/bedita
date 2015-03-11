<fieldset id="properties">	

{$view->element('form_common_js')}

{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
	
	<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	<input type="hidden" name="data[title]" value="{$object.title|default:''|escape}"/>

	<table class="areaform" border=0 style="margin-bottom:10px">

		<tr>
			<th>{t}title{/t}:</th>
			<td>
				<input id="titleBEObject" type="text" name="data[title]" value="{$object.title|default:''|escape:'html'|escape:'quotes'}" />
			</td>
		</tr>
		<tr>
			<th>{t}public name{/t}:</th>
			<td>
				<input type="text" name="data[public_name]" value="{$object.public_name|default:''|escape:'html'|escape:'quotes'}"/>
			</td>
		</tr>
		<tr>
			<th>{t}description{/t}:</th>
			<td>
				<textarea class="mceSimple" name="data[description]">{$object.description|default:''|escape:'html'|escape:'quotes'}</textarea>
			</td>
		</tr>
        <tr>
            <th>{t}status{/t}:</th>
            <td id="status">
                {if $object.fixed}
                    {t}This object is fixed - some data is readonly{/t}
                    <br />
                    {html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;" disabled="disabled"}
                {else}
                    {html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
                {/if}

                {if in_array('administrator', $BEAuthUser.groups)}
                    &nbsp;&nbsp;&nbsp;
                    <b>fixed</b>:
                    &nbsp;&nbsp;
                    <input type="hidden" name="data[fixed]" value="0" />
                    <input type="checkbox" name="data[fixed]" value="1" {if !empty($object.fixed)}checked{/if} />
                {else}
                    <input type="hidden" name="data[fixed]" value="{$object.fixed}" />
                {/if}
            </td>
        </tr>
		<tr>
			<th>syndicate:</th>
			<td>
					<div class="ico_rss {if $object.syndicate|default:'off'=='on'}on{/if}" 
					style="float:left; vertical-align:middle; margin-right:10px; width:24px; height:24px;">&nbsp;</div>
					<input style="margin-top:4px" type="checkbox" 
					onclick="$('.ico_rss').toggleClass('on')"
					name="data[syndicate]" value="on" {if $object.syndicate|default:'off'=='on'}checked{/if} />
			</td>
		</tr>
		<tr>
			<th>{t}order{/t}:</th>
			<td>
				<input type="radio" name="data[priority_order]" value="asc" {if $object.priority_order|default:'asc'=="asc"}checked{/if} />{t}Insertion order, oldest contents first{/t}
				<input type="radio" name="data[priority_order]" value="desc" {if $object.priority_order|default:'asc'=="desc"}checked{/if} />{t}Latest/newest contents first{/t}
			</td>
		</tr>
	</table>
	


<div class="tab"><h2>{t}More properties{/t}</h2></div>

<div id="moreproperties">
	<table class="areaform">
		<tr>
			<th>{t}main language{/t}:</th>
			<td>
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
	<tr>
		<th>{t}nickname{/t}:</th>
		<td>
			<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
			</tr>
				<tr>
				<th>id:</th>
				<td>{$object.id|default:null}</td>
			</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th>{t}public url{/t}:</th>
		<td>
			<input type="text" name="data[public_url]" value="{$object.public_url|default:''|escape}""/>
		</td>
	</tr>
	
	<tr>
		<th>{t}staging url{/t}:</th>
		<td>
			<input type="text" name="data[staging_url]" value="{$object.staging_url|default:''|escape}""/>
		</td>
	</tr>
	<tr>
		<th>{t}contact email{/t}:</th>
		<td>
			<input type="text" name="data[email]" value="{$object.email|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>
		</td>
	</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th>{t}creator{/t}:</th>
		<td>
			<input type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Creator is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}publisher{/t}:</th>
		<td>
			<input type="text" name="data[publisher]" value="{$object.publisher|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Publisher is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}rights{/t}:</th>
		<td>
			<input type="text" name="data[rights]" value="{$object.rights|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Rights is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<td> <label>{t}license{/t}:</label></td>
		<td>
			<select name="data[license]">
				<option value="">--</option>
				{foreach from=$conf->defaultLicenses item=lic key=code}
					<option value="{$code}" {if $object.license==$code}selected="selected"{/if}>{$lic.title}</option>
				{/foreach}
				{foreach from=$conf->cfgLicenses item=lic key=code}
					<option value="{$code}" {if $object.license==$code}selected="selected"{/if}>{$lic.title}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	</table>

</div>

<div class="tab"><h2>{t}Statistics{/t}</h2></div>
	<fieldset id="statistics">
	<table class="areaform">
	<tr>
		<th>{t}Provider{/t}:</th>
		<td>
			<select name="data[stats_provider]">
				<option value="GoogleAnalytics" {if "GoogleAnalytics"==$object.stats_provider}selected="selected"{/if}>Google analytics</option>
				<option value="PWik" {if "piwik"==$object.stats_provider}selected="selected"{/if}>PWik</option>
				<option value="" {if empty($object.stats_provider)}selected="selected"{/if}>Nessuno</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>{t}Provider URL{/t}:</th>
		<td>
			<input type="text" name="data[stats_provider_url]" value="{$object.stats_provider_url|default:''|escape}"/>
			{if isset($object.stats_provider_url)}
			<a href="{$object.stats_provider_url|escape}" target="_blank">
			› access statistics
			</a>
			{/if}
		</td>
	</tr>
	<tr>
		<th>{t}Code{/t}:</th>
		<td colspan="2">
			<textarea name="data[stats_code]" class="autogrowarea" style="font-size:0.8em; color:gray; width:470px;">{$object.stats_code|default:''|escape}</textarea>
		</td>
	</tr>
	</table>
		
	</fieldset>

	{$view->element('form_assoc_objects',['object_type_id' => {$conf->objectTypes.area.id}])}

	{assign_associative var="params" object=$object|default:null}
	{$view->element('form_translations', $params)}

	{assign var='excludedParts' value=','|explode:"publisher,rights,license"}
    {$view->element('form_advanced_properties', ['el' => $object, 'excludedParts' => $excludedParts])}

	{$view->element('form_custom_properties')}
	
	{assign_associative var="params" el=$object|default:null recursion=true}
	{$view->element('form_permissions', $params)}
	
	{$view->element('form_versions')}
