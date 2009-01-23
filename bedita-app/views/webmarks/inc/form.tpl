{*
** document form template
*}


{* title and description *}

<div class="tab"><h2>{t}Title{/t}</h2></div>

<fieldset id="title">

	<label>{t}url{/t}:</label>
	<br />
	<input style="width:460px" type="text" name="data[url]" value="{$object.url|default:''}" />
	<br />
	<label>{t}title{/t}:</label>
	<br />
	<input style="width:460px" type="text" name="data[title]" value="{$object.title|default:''}" />
	<br />
	<label>{t}description{/t}:</label>
	<br />
	<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''}</textarea>

</fieldset>

	
<div class="tab"><h2>{t}Properties{/t}</h2></div>
<fieldset id="linkdetails">
	
	
		
	<table class="bordered">
		<tr>
			<th>{t}Status{/t}:</th>
			<td>
				{html_radios name="data[status]" options=$conf->statusOptions selected=$tag.status|default:$conf->status separator="&nbsp;"}
			</td>
		</tr>
		<tr>
			<th>404 Check this url:</th><td><input type="button" value="ping" /> <b>result: </b><span class="alert">OK!</span></td>
		</tr>
		<tr>
			<th style="vertical-align:top">{t}referenced objects list{/t}:</th>
			<td>
				{if !empty($referenced)}
				
				<ul>

					{foreach from=$referenced item="ref"}
						<li>
						<span class="listrecent {$ref.ObjectType.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
						<a title="{$ref.created}" href="{$html->url('/')}{$ref.ObjectType.module}/view/{$ref.id}">{$ref.title}</a>
						</li>
					{/foreach}

				</ul>
				{/if}
			</td>
		</tr>
	</table>
	
</fieldset>

	{include file="../common_inc/form_categories.tpl"}