<div class="tab"><h2>{t}{$title|default:'Title and properties'}{/t}</h2></div>

<fieldset id="properties">

{$view->element('form_common_js')}

{$view->element('texteditor')}

<input type="hidden" name="data[id]" value="{$object.id|default:null}"/>
	
	<table class="areaform">

			<tr>
				<th>{t}title{/t}:</th>
				<td><input type="text" id="titleBEObject" style="width:100%" name="data[title]" value="{$object.title|default:""|escape}"/></td>
			</tr>
            <tr>
                <th>{t}status{/t}:</th>
                <td id="status">
                    {if $object.fixed|default:0}
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
				<th>{t}unique name{/t}<br />({t}url name{/t}):</th>
				<td>
					<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:null}" />
				</td>
			</tr>
			<tr>
				<th>{t}creator{/t}:</th>
				<td>
					<input style="width:100%" type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}" />
				</td>
				
			</tr>	
			<tr>
				<th>{t}main language{/t}:</th>
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
			<th>{t}visibility{/t}:</th>
			<td>
				<input type="checkbox" name="data[menu]" value="1" {if $object.menu|default:1 != '0'}checked{/if}/>
				 {t}Visible in menu and canonical paths{/t}
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
	
</fieldset>