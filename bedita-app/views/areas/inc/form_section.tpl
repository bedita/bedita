<fieldset id="properties">

{$javascript->link("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}

{$view->element('form_common_js')}

<input type="hidden" name="data[id]" value="{$object.id|default:null}"/>
	
	<table class="areaform">

			<tr>
				<th>{t}title{/t}:</th>
				<td><input type="text" id="titleBEObject" style="width:100%" name="data[title]" value="{$object.title|default:""}"/></td>
			</tr>
			<tr>
				<td><label>{t}reside in{/t}:</label></td>
				<td>
					<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[parent_id]">
					{if !empty($parent_id)}
						{$beTree->option($tree, $parent_id)}
					{else}
						{$beTree->option($tree)}
					{/if}
					</select>
					
					{if $object|default:false && ($object.fixed == 1)}
						<input id="areaSectionAssoc" type="hidden" name="data[parent_id]" value="{$parent_id}" />
					{/if}
					
				</td>
			</tr>
			<tr>
					<th>{t}description{/t}:</th>
					<td><textarea style="width:100%" class="mceSimple" name="data[description]">{$object.description|default:""}</textarea></td>
			</tr>
			<tr>
			
					<th>{t}status{/t}:</th>
					<td id="status">
				{if $object.fixed|default:'' == 1}
					{t}This object is fixed - some data is readonly{/t}
					<input type="hidden" name="data[status]" value="{$object.status}" />
				{else}
					{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator=" "}
				{/if}	
			{if in_array('administrator',$BEAuthUser.groups)}
				&nbsp;&nbsp;&nbsp; <b>fixed</b>:&nbsp;&nbsp;<input type="checkbox" name="data[fixed]" value="1" {if !empty($object.fixed)}checked{/if} />
			{else}
				<input type="hidden" name="data[fixed]" value="{$object.fixed|default:0}" />
			{/if}	
						
					</td>
			</tr>
			<tr>
				<th>
					syndicate:
				</th>
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
						<input type="radio" name="data[priority_order]" value="asc" {if $object.priority_order|default:'asc'=="asc"}checked{/if} />{t}asc{/t}
						<input type="radio" name="data[priority_order]" value="desc" {if $object.priority_order|default:'asc'=="desc"}checked{/if} />{t}desc{/t}
					</td>
			</tr>
	</table>
	
</fieldset>

	<br />
<fieldset>	

<div class="tab"><h2>{t}More properties{/t}</h2></div>

<div>
	
	<table class="areaform">

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
			<tr>
				<th>{t}creator{/t}:</th>
				<td>
					<input style="width:100%" type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}"
					class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Creator is required (at least %1 alphanumerical char){/t}"/>
				</td>
				
			</tr>	
			<tr>
				<td><label>{t}publisher{/t}:</label></td>
				<td><input type="text" name="data[publisher]" value="{$object.publisher|default:null}" /></td>
			</tr>
			<tr>
				<td><strong>&copy; {t}rights{/t}:</strong></td>
				<td><input type="text" name="data[rights]" value="{$object.rights|default:null}" /></td>
			</tr>
			<tr>
				<td> <label>{t}license{/t}:</label></td>
				<td>
					<select style="width:280px" name="data[license]">
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
			<tr>
				<th>{t}nickname{/t}:</th>
				<td>
					<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:null}" />
				</td>
			</tr>
		{if !empty($object.Alias)}
			<tr>
				<th>{t}Alias{/t}:</th>
				<td>
					<ul>
					{foreach from=$object.Alias item=alias}
						{$alias.nickname_alias}
					{/foreach}
					</ul>
				</td>
			</tr>
		{/if}			
			<tr>
				<th>id:</th>
				<td>{$object.id|default:null}</td>
			</tr>
		</table>
	

</div>	
	
	{assign_associative var="params" object=$object|default:null}
	{$view->element('form_translations', $params)}

	{$view->element('form_custom_properties')}
	
	{assign_associative var="params" el=$object|default:null recursion=true}
	{$view->element('form_permissions', $params)}

	{$view->element('form_versions')}
</fieldset>
