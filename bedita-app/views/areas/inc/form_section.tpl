{$javascript->link("jquery/jquery.autogrow")}

<script language="JavaScript">
	{literal}
	$(document).ready( function ()
	{
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
		{/literal}
		{if !empty($section.id) && ($section.status == 'fixed')}
			{literal}
			$("#titleBEObject").attr("readonly",true);
			$("#nicknameBEObject").attr("readonly",true);
			$("#areaSectionAssoc").focus( function() {
				alert('status fixed: not modifyable');
				body.focus();
				this.blur();
			});
			$("#delBEObject").attr("disabled",true);
			{/literal}
		{else}
			{literal}
			$("#titleBEObject").attr("readonly",false);
			$("#nicknameBEObject").attr("readonly",false);
			$("#areaSectionAssoc").focus( function() {});
			$("#delBEObject").attr("disabled",false);
			{/literal}
		{/if}
		{literal}
	});
	{/literal}
</script>

<input type="hidden" name="data[id]" value="{$section.id|default:null}"/>
<table>
			
			<tr>
			
					<th>{t}Status{/t}:</th>
					<td>
						{if (!empty($section) && $section.status == 'fixed')}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[status]" value="fixed"/>
						{else}
						{html_radios name="data[status]" options=$conf->statusOptions selected=$section.status|default:$conf->status separator="&nbsp;"}
						{/if}
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
					<td><input type="text" style="width:280px" id="titleBEObject" name="data[title]" value="{$section.title|default:""}"/></td>
				</tr>
				<tr>
					<th>{t}Description{/t}</th>
					<td><textarea style="width:280px" class="autogrowarea" name="data[description]">{$section.description|default:""}</textarea></td>
			</tr>

			<tr>
				<td><label>{t}reside in{/t}</label></td>
				<td>
					<select id="areaSectionAssoc" class="areaSectionAssociation" style="width:280px" name="data[parent_id]">
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
				<td><input type="text" style="width:280px" name="publisher" value="" /></td>
			</tr>
			<tr>
				<td><strong>&copy; {t}rights{/t}</strong></td>
				<td><input type="text" style="width:280px" name="data[rights]" value="{$section.rights|default:null}" /></td>
			</tr>
			<tr>
				<td> <label>{t}license{/t}</label></td>
				<td>
					<select style="width:280px" name="data[license]">
						<option value="">--</option>
						<option  value="Creative Commons Attribuzione 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione 2.5 Italia"} selected{/if}>Creative Commons Attribuzione 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione-Non commerciale 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non opere derivate 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione-Non opere derivate 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia"{if !empty($section) && $section.license == "Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
						<option  value="Tutti i diritti riservati"{if !empty($section) && $section.license == "Tutti i diritti riservati"} selected{/if}>Tutti i diritti riservati</option>
					</select>
				</td>
			</tr>

			<tr>
				<th>{t}Nickname{/t}</th>
				<td><input id="nicknameBEObject" type="text" style="width:280px" name="data[nickname]" value="{$section.nickname|default:null}"/></td>
			</tr>

			</table>

			<br/>
			<div class="indexlist">
			{include file="../common_inc/form_translations.tpl" object=$section|default:null}
			</div>
			{include file="../common_inc/form_permissions.tpl" el=$section|default:null recursion=true}
			{include file="../common_inc/form_custom_properties.tpl" el=$section|default:null}


			<hr />
			oppure per i dettagli tipo custom pop e permessi linkare l'ulteriore dettaglio.?.
			<a href="{$html->url('viewSection/')}"> QUI</a>