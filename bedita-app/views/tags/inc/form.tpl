
<input type="hidden" name="data[id]" value="{$tag.id|default:''}"/>
<input type="hidden" name="tags_selected[0]" value="{$tag.id|default:''}"/>
<input type="hidden" name="data[name]" value="{$tag.name|default:''}"/>


<div class="tab"><h2>{t}Tag details{/t}</h2></div>
<fieldset id="tagdetails">
	<table class="bordered">
		<tr>
			<th>{t}Label{/t}:</th>
			<td>
				<input type="text" name="data[label]" value="{$tag.label|default:''|escape:'html'|escape:'quotes'}"
				class="{ required:true,minLength:1}" title="{t 1='1'}Name is required (at least %1 alphanumerical char){/t}"/>
			</td>
		</tr>
		<tr>
			<th>{t}Unique name{/t}:</th>
			<td>
				{if in_array('administrator', $BEAuthUser.groups)}
					<input type="text" name="data[name]" value="{$tag.name|default:''}"/>
				{else}
					{$tag.name}
				{/if}
			</td>
		</tr>
		<tr>
			<th>{t}Status{/t}:</th>
			<td>
				{html_radios name="data[status]" options=$conf->statusOptions selected=$tag.status|default:$conf->defaultStatus separator="&nbsp;"}
			</td>
		</tr>
		<tr>
			<th>{t}Occurrences{/t}:</th>
			<td>
				{$tag.weight|default:""}
			</td>
		</tr>
		<tr>
			<th style="vertical-align:top">{t}referenced objects list{/t}:</th>
			<td>
				{if !empty($referenced)}
				
				<ul>

					{foreach from=$referenced item="ref"}
						<li>
						<span class="listrecent {$ref.ObjectType.module_name}" style="margin-left:0px">&nbsp;&nbsp;</span>
						<a title="{$ref.created}" href="{$html->url('/')}{$ref.ObjectType.module_name}/view/{$ref.id}">{$ref.title|default:'<i>[no title]</i>'|escape}</a>
						</li>
					{/foreach}

				</ul>
				{/if}
			</td>
		</tr>
	</table>

</fieldset>