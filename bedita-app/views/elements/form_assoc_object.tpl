{foreach from=$objsRelated item="objRelated" name="assocForeach"}
<tr class="obj {$objRelated.status|default:''}" data-beid="{$objRelated.id}">
	<td style="padding:0px; width:20px;">
		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
		{if !empty($objRelated.uri) && $objRelated.ObjectType.name=="image"}
		{assign_associative var="bkgparams" URLonly=true}
		<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
		{else}
			{if !empty($objRelated.thumbnail) && $objRelated.ObjectType.name=="video"}
			{assign_associative var="bkgparams" URLonly=true}
			<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
			{/if}
		{/if}
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>

	<td style="width:10px;">
		<span title="{$objRelated.ObjectType.name}" class="listrecent {$objRelated.ObjectType.module_name|default:''}" style="margin:0px">&nbsp;</span>
	</td>
	
	<td class="assoc_obj_title">{$objRelated.title|default:'<i>[no title]</i>'|truncate:60:'~':true}</td>

{if $rel == "download"}

	<td>{$objRelated.mime_type|default:''|truncate:60:'~':true}</td>
	
	<td style="text-align:right">{$objRelated.file_size|default:0|filesize}</td>

{/if}

	<td>{$objRelated.status|default:''}</td>
	
	<td>{$objRelated.lang|default:''}</td>

	{if !empty($conf->defaultObjRelationType[$rel])}
		{$relationParamsArray = $conf->defaultObjRelationType[$rel].params|default:[]}
	{else}
		{$relationParamsArray = $conf->objRelationType[$rel].params|default:[]}
	{/if}

	{if !empty($relationParamsArray[0])}
	<td style="width: 40%" class="relparams">
		<input class="BEbutton" type="button" value="show/hide params" onclick="$(event.target).parent().find('table').toggle()" />
		<br />
		<table style="display: none">
		{foreach $relationParamsArray as $paramKey => $paramVal}
			{if is_array($paramVal)}
				{$paramName = $paramKey}
			{else}
				{$paramName = $paramVal}
			{/if}
			<tr>
				<td>
					<label for="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]">{$paramName}</label>
				</td>
				<td>
					{if is_array($paramVal)}
						<select name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]">
							{foreach $paramVal as $paramOpt}
								<option value="{$paramOpt}" {if $objRelated.params[$paramName]|default:"" == $paramOpt}selected{/if}>{$paramOpt}</option>
							{/foreach}
						</select>
					{else}
						<input type="text" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]" value="{$objRelated.params[$paramName]|default:""}" />
					{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</td>
	{/if}
	
	<td style="text-align:right; white-space:nowrap">
		<a class="BEbutton golink" 
		title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$objRelated.ObjectType.module_name}/view/{$objRelated.id}">{t}details{/t}</a>
		<input class="BEbutton" name="remove" type="button" value="x">
	</td>

</tr>
{/foreach}