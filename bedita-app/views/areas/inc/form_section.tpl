<fieldset id="properties">

{literal}
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	
	$(".autogrowarea").autogrow({
		lineHeight: 16
	});
	$(".areaform input[type='text'], .areaform textarea").width(340);
	

});
</script>
{/literal}

{include file="../common_inc/form_common_js.tpl"}


<input type="hidden" name="data[id]" value="{$object.id|default:null}"/>
<input type="hidden" name="data[fixed]" value="{$object.fixed|default:0}"/>
	
	<table class="areaform">

			<tr>
				<th>{t}title{/t}:</th>
				<td><input type="text" id="titleBEObject" style="width:340px;" name="data[title]" value="{$object.title|default:""}"/></td>
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
					<td><textarea class="autogrowarea" name="data[description]">{$object.description|default:""}</textarea></td>
			</tr>
			<tr>
			
					<th>{t}status{/t}:</th>
					<td id="status">
						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
					</td>
			</tr>
			<tr>
				<th>
					syndicate:
				</th>
				<td>
					<div class="ico_rss" style="float:left; vertical-align:middle; margin-right:10px; width:24px; height:24px;">&nbsp;</div>
					<input style="margin-top:4px" type="checkbox" name="data[syndicate]" value="on" {if $object.syndicate|default:'off'=='on'}checked{/if} />
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
			<tr>
				<th>{t}nickname{/t}:</th>
				<td>
					<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:null}" />
		
				</td>
			</tr>
				<tr>
				<th>id:</th>
				<td>{$object.id|default:null}</td>
			</tr>
		</table>
	

</div>	


	{include file="../common_inc/form_translations.tpl" object=$object|default:null}
	{include file="../common_inc/form_custom_properties.tpl"}
	{include file="../common_inc/form_permissions.tpl" el=$object|default:null recursion=true}
	
