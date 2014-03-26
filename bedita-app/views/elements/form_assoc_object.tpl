{foreach from=$objsRelated item="objRelated" key=key name="assocForeach"}

<tr class="obj {$objRelated.status|default:''}" data-beid="{$objRelated.id}">
	<td>
		{if !empty($objRelated.uri) && $objRelated.ObjectType.name=="image"}
		{assign_associative var="bkgparams" URLonly=true}
		<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
		{else}
			{if !empty($objRelated.thumbnail) && $objRelated.ObjectType.name=="video"}
			{assign_associative var="bkgparams" URLonly=true}
			<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
			{/if}
		{/if}

		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority {$objRelated.ObjectType.module_name|default:''}" 
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>

{if $objRelated.ObjectType.module_name == "multimedia"}
	<td class="filethumb">
		{assign_associative var="params" presentation="thumb" width='135'}
		{$beEmbedMedia->object($objRelated,$params)}
	</td>
{/if}

	<td class="assoc_obj_title">
		{$objRelated.title|default:'<i>[no title]</i>'|truncate:60:'~':true}
	</td>

{if $rel == "download" or $rel == "attach"}
	
	<td class="mimetype" >{$objRelated.mime_type|default:'&nbsp;'|truncate:60:'~':true}</td>
	<td class="filesize">{$objRelated.file_size|default:0|filesize}</td>

{/if}

	<td class="status">{$objRelated.status|default:''}</td>
	
	<td class="lang">{$objRelated.lang|default:''}</td>

	<td class="commands">

	{if !empty($allObjectsRelations[$rel])}
		{$relationParamsArray = $allObjectsRelations[$rel].params|default:[]}
	{else}
		{foreach $allObjectsRelations as $relName => $rule}
			{if !empty($rule.inverse) && $rule.inverse == $rel}
				{$relationParamsArray = $rule.params|default:[]}
			{/if}
		{/foreach}
	{/if}

	{if $rel == "attach"}
		<input class="BEbutton" type="button" value="D" onclick="$(event.target).closest('.obj').find('.description').toggle()" />
	{/if}

	{if !empty($relationParamsArray[0])}
		<input class="BEbutton" type="button" value="P" onclick="$(event.target).closest('.obj').find('.relparams').toggle()" />
	{/if}
		
		<a class="BEbutton golink" title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$objRelated.ObjectType.module_name}/view/{$objRelated.id}">G</a>	
		
		<input class="BEbutton" name="remove" type="button" value="x">
	
	{if !empty($relationParamsArray[0])}
		<div class="relparams" style="display:none">
			<table>
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
		</div>
	{/if}
	
	{if $rel == "attach"}
	<div class="description" style="display:none">
		<label>description</label>
		<textarea name="data[RelatedObject][{$rel}][{$objRelated.id}][description]">{$objRelated.description|default:''}</textarea>
	</div>
	{/if}

	</td>
</tr>
{/foreach}