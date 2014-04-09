{* included by show_objects.tpl *}
{strip}

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
		
		<input type="hidden" class="mod" name="data[RelatedObject][{$rel}][{$objRelated.id}][modified]" value="0" />
		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority {$objRelated.ObjectType.module_name|default:''}" 
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>

	<td class="filethumb">
	{if $objRelated.ObjectType.module_name == "multimedia"}
		{assign_associative var="params" presentation="thumb" width='135' height='85'}
		{$beEmbedMedia->object($objRelated,$params)}
	{/if}
	</td>

	<td class="assoc_obj_title" data-inputname="data[RelatedObject][{$rel}][{$objRelated.id}][title]">
		<h4>{$objRelated.title|default:'<i>[no title]</i>'|truncate:60:'~':true}</h4>
		
		<textarea style="display:none" placeholder="{t}title{/t}" name="data[RelatedObject][{$rel}][{$objRelated.id}][title]">{$objRelated.title|default:''}</textarea>

	</td> 

{if $rel == "question"}
	<td class="">{$objRelated.question_type|default:''}</td>
{/if}

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

{if !empty($relationParamsArray)}
		<a class="BEbutton showmore">+</a>	
{/if}
		<a class="BEbutton golink" title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$objRelated.ObjectType.module_name}/view/{$objRelated.id}"></a>	
		
		<a class="BEbutton remove">x</a>

		<div class="moredata" style="display:none">
			{if !empty($relationParamsArray[0])}
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
								<input type="text" placeholder="{$paramName}" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]" value="{$objRelated.params[$paramName]|default:""}" />
							{/if}
						</td>
					</tr>
				{/foreach}
				</table>
			{/if}
			{if in_array($objRelated.object_type_id,$conf->objectTypes['multimedia']['id'])}
			<div class="description">
				<label>{t}description{/t}</label>
				<textarea placeholder="{t}description{/t}" name="data[RelatedObject][{$rel}][{$objRelated.id}][description]">{$objRelated.description|default:''}</textarea>
			</div>
			{/if}
		</div>

	</td>
</tr>
{/foreach}
{/strip}
