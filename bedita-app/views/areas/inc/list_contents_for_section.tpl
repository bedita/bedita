{* included in form_assoc_objects *}
{strip}

{foreach from=$objsRelated item="objRelated" key=key name="assocForeach"}

{assign_associative var='ObjectType' name=$conf->objectTypes[$objRelated.object_type_id].module_name}

<tr class="obj {$objRelated.status|default:''}" data-beid="{$objRelated.id}" data-benick="{$objRelated.nickname}">
	<td>
		{if !empty($objRelated.start_date) && ($objRelated.start_date|date_format:"%Y%m%d") > ($smarty.now|date_format:"%Y%m%d")}
			
			<img title="{t}object scheduled in the future{/t}" src="{$html->webroot}img/iconFuture.png" style="height:28px; vertical-align:top;">
		
		{elseif !empty($objRelated.end_date) && ($objRelated.end_date|date_format:"%Y%m%d") < ($smarty.now|date_format:"%Y%m%d")}
		
			<img title="{t}object expired{/t}" src="{$html->webroot}img/iconPast.png" style="height:28px; vertical-align:top;">
		
		{elseif (!empty($objRelated.start_date) && (($objRelated.start_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d"))) or ( !empty($objRelated.end_date) && (($objRelated.end_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d")))}
		
			<img title="{t}object scheduled today{/t}" src="{$html->webroot}img/iconToday.png" style="height:28px; vertical-align:top;">

		{/if}
		
		{if !empty($objRelated.num_of_permission)}
			<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; vertical-align:top;">
		{/if}
		
		{if (!empty($objRelated.fixed))}
			<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
		{/if}

		{if !empty($objRelated.uri) && $ObjectType.name=="image"}
		{assign_associative var="bkgparams" URLonly=true}
		<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
		{else}
			{if !empty($objRelated.thumbnail) && $ObjectType.name=="video"}
			{assign_associative var="bkgparams" URLonly=true}
			<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated, $bkgparams)}">
			{/if}
		{/if}
			
		{if !empty($rel)}
		<input type="hidden" class="mod" name="data[RelatedObject][{$rel}][{$objRelated.id}][modified]" value="0" />
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority {$ObjectType.name|default:''}" 
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>

		{else}
		<input style="margin-top: -2px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objRelated.id}" value="{$objRelated.id}" />
		<input type="hidden" class="id" name="reorder[{$objRelated.id}][id]" value="{$objRelated.id}" />
		<input type="text" class="priority {$ObjectType.name}" name="reorder[{$objRelated.id}][priority]" value="{$objRelated.priority|default:""}" />
		{/if}
		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
	</td>

	<td class="filethumb">
	{if !empty($objRelated.uri)}
		{assign_associative var="params" presentation="thumb" width='155'}
		{$beEmbedMedia->object($objRelated,$params)}
	{/if}
	</td>

	<td class="assoc_obj_title"{if !empty($rel)} data-inputname="data[RelatedObject][{$rel}][{$objRelated.id}][title]"{/if}>
		<h4>{$objRelated.title|escape|default:'<i>[no title]</i>'|truncate:60:'~':true}</h4>
		{if !empty($rel)}
		<input type="text" style="display:none" placeholder="{t}title{/t}" name="data[RelatedObject][{$rel}][{$objRelated.id}][title]" value="{$objRelated.title|default:''}">
		{/if}
	</td> 

{if !empty($rel) && $rel == "question"}
	<td>{$objRelated.question_type|default:''}</td>
{/if}

{if $ObjectType.name == "multimedia"}
	{$calctype = $objRelated.Category.0.name|default:$ObjectType.name} 
{else}
	{$calctype = $ObjectType.name} 
{/if}

	<td title="{$calctype}" class="obtype"><span class="icon-{$calctype}"></span></td>

	<td class="status">{$objRelated.status|default:''}</td>
	
	<td class="lang">{$objRelated.lang|default:''}</td>

	<td nowrap class="mimetype">
		{if !empty($objRelated.file_size)}{$objRelated.file_size|default:0|filesize}&nbsp;&nbsp;{/if} {$objRelated.mime_type|default:''|truncate:60:'~':true}
	</td>

	<td class="moredata">
		<div style="display: none">
		{if !empty($rel)}
			{if !empty($allObjectsRelations[$rel])}
				{$relationParamsArray = $allObjectsRelations[$rel].params|default:[]}
			{else}
				{foreach $allObjectsRelations as $relName => $rule}
					{if !empty($rule.inverse) && $rule.inverse == $rel}
						{$relationParamsArray = $rule.params|default:[]}
					{/if}
				{/foreach}
			{/if}
			{if !empty($relationParamsArray[0])}
				{foreach $relationParamsArray as $paramKey => $paramVal}
					{if is_array($paramVal)}
						{$paramName = $paramKey}
					{else}
						{$paramName = $paramVal}
					{/if}
					<label for="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]">{$paramName}</label>
					{if is_array($paramVal)}
						<select name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]">
							{foreach $paramVal as $paramOpt}
								<option value="{$paramOpt}" {if $objRelated.params[$paramName]|default:"" == $paramOpt}selected{/if}>{$paramOpt}</option>
							{/foreach}
						</select>
					{else}
						<input type="text" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][params][{$paramName}]" value="{$objRelated.params[$paramName]|default:""}" />
					{/if}
				{/foreach}
			{/if}
			{if in_array($objRelated.object_type_id,$conf->objectTypes['multimedia']['id'])}
			<label>description</label>
			<textarea placeholder="{t}description{/t}" name="data[RelatedObject][{$rel}][{$objRelated.id}][description]">{$objRelated.description|default:''}</textarea>
			{/if}
		{/if}
		</div>
	</td>

	<td class="commands">

{if !empty($rel) && !empty($relationParamsArray)}
		<a class="BEbutton showmore">+</a>	
{/if}
		<a class="BEbutton golink" title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$ObjectType.name}/view/{$objRelated.id}"></a>	
		
		<a class="BEbutton remove">x</a>

	</td>
</tr>
{/foreach}
{/strip}