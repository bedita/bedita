<input type="hidden" name="data[id]" value="{$section.id|default:null}"/>
<table>
			
			<tr>
			
					<th>{t}Status{/t}:</th>
					<td>
						{html_radios name="data[status]" options=$conf->statusOptions 
						selected=$section.status|default:$conf->status separator="&nbsp;"}
					</td>
			
				</tr>
			<tr>
					<th>{t}language{/t}:</th>
					<td>
					{assign var=object_lang value=$section.lang|default:$conf->defaultLang}
					<select name="data[lang]" id="main_lang">
						{foreach key=val item=label from=$conf->langOptions name=langfe}
						<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
						{/foreach}
					</select>
					</td>
				</tr>
				<tr>
					<th>{t}Title{/t}</th>
					<td><input type="text" name="data[title]" value="{$section.title|default:""}" /></td>
				</tr>
				<tr>
					<th>{t}Description{/t}</th>
					<td><textarea style="height:30px" class="autogrowarea" name="data[description]">{$section.description|default:""}</textarea></td>
			</tr>

			<tr>
				<td><label>reside in</label></td>
				<td>
					<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[parent_id]">
					{if !empty($parent_id)}
						{$beTree->option($tree, $parent_id)}
					{else}
						{$beTree->option($tree)}
					{/if}
					</select>
				</td>
			</tr>
			
			<tr>
				<td><label>{t}publisher{/t}</label></td>
				<td><input type="text" name="publisher" value="" /></td>
			</tr>
			<tr>
					<td><strong>&copy; {t}rights{/t}</strong></td>
				<td><input type="text" name="rights" value="" /></td>
			</tr>
			<tr>
				<td> <label>{t}license{/t}</label></td>                
				<td>
					<select style="width:200px;" name="license">
						<option value="">--</option>
						<option  value="1">Creative Commons Attribuzione 2.5 Italia</option>
						<option  value="2">Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
						<option  value="3">Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="4">Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
						<option  value="5">Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="6">Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
						<option  value="7">Tutti i diritti riservati</option>
					</select>
			    </td>
			</tr>
			
			<tr>
				<th>{t}Nickname{/t}</th>
				<td><input type="text" name="data[nickname]" value="{$section.nickname|default:""}" /></td>
			</tr>
			
			</table>         
			
			<br>
			
			{include file="../common_inc/form_permissions.tpl" el=$section|default:null recursion=true}
			{include file="../common_inc/form_custom_properties.tpl" el=$section|default:null}
			
			
			<hr />
			oppure per i dettagli tipo custom pop e permessi linkare l'ulteriore dettaglio.?.
			<a href="{$html->url('viewSection/')}"> QUI</a>