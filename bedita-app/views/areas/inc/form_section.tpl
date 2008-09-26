{if !empty($object)}

<script type="text/javascript">
	{literal}
	$(document).ready( function ()
	{
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
		{/literal}
		{if !empty($object.id) && ($object.status == 'fixed')}
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

{/if}


<fieldset id="properties">

<input type="hidden" name="data[id]" value="{$object.id|default:null}"/>
	
	<table class="areaform">

			<tr>
				<th>{t}title{/t}:</th>
				<td><input type="text" id="titleBEObject" name="data[title]" value="{$object.title|default:""}"/></td>
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
				</td>
			</tr>
			<tr>
					<th>{t}description{/t}:</th>
					<td><textarea class="autogrowarea" name="data[description]">{$object.description|default:""}</textarea></td>
			</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
			<tr>
			
					<th>{t}status{/t}:</th>
					<td>
						{if (!empty($object) && $object.status == 'fixed')}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[status]" value="fixed"/>
						{else}
						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
						{/if}
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
					</select>
					</td>
				</tr>
			<tr>
				<th>{t}nickname{/t}:</th>
				<td>
					<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:null}" />
					{$object.id|default:null}
				</td>
			</tr>

	</table>
	
	<hr />
	
	<table class="areaform">
			
			<tr>
				<td><label>{t}publisher{/t}:</label></td>
				<td><input type="text" name="publisher" value="" /></td>
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
						<option  value="Creative Commons Attribuzione 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione 2.5 Italia"} selected{/if}>Creative Commons Attribuzione 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione-Non commerciale 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non opere derivate 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione-Non opere derivate 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia"{if !empty($object) && $object.license == "Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia"} selected{/if}>Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
						<option  value="Tutti i diritti riservati"{if !empty($object) && $object.license == "Tutti i diritti riservati"} selected{/if}>Tutti i diritti riservati</option>
					</select>
				</td>
			</tr>

		</table>

</fieldset>	


{if (!empty($method) && $method == "viewSection")}			

	{include file="../common_inc/form_translations.tpl" object=$object|default:null}
	{include file="../common_inc/form_custom_properties.tpl" el=$object|default:null}
	{include file="../common_inc/form_permissions.tpl" el=$object|default:null recursion=true}

{else}
	
	<hr />
	<a href="{$html->url('/areas/viewSection/')}{$object.id}">
	
		{t}Edit more details{/t}

	</a>
	<hr />
{/if}


