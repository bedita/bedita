{* included in form_assoc_objects *}
{strip}

{foreach from=$objsRelated item="objRelated" key=key name="assocForeach"}

{$objectTypeModule = $conf->objectTypes[$objRelated.object_type_id].module_name}
{$objectType =$conf->objectTypes[$objRelated.object_type_id].name}


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
{/if}

<tr class="obj {$objRelated.status|default:''}" data-beid="{$objRelated.id}" data-benick="{$objRelated.nickname}">
	<td class="priority-column">
		{if !empty($objRelated.start_date) && ($objRelated.start_date|date_format:"%Y%m%d") > ($smarty.now|date_format:"%Y%m%d")}
			
			<img title="{t}object scheduled in the future{/t}" src="{$html->webroot}img/iconFuture.png" style="height:28px; vertical-align: middle;">
		
		{elseif !empty($objRelated.end_date) && ($objRelated.end_date|date_format:"%Y%m%d") < ($smarty.now|date_format:"%Y%m%d")}
		
			<img title="{t}object expired{/t}" src="{$html->webroot}img/iconPast.png" style="height:28px; vertical-align: middle;">
		
		{elseif (!empty($objRelated.start_date) && (($objRelated.start_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d"))) or ( !empty($objRelated.end_date) && (($objRelated.end_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d")))}
		
			<img title="{t}object scheduled today{/t}" src="{$html->webroot}img/iconToday.png" style="height:28px; vertical-align: middle;">

		{/if}
		
		{if !empty($objRelated.num_of_permission)}
			<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; vertical-align: middle;">
		{/if}
		
		{if (!empty($objRelated.fixed))}
			<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
		{/if}

		{if !empty($objRelated.uri) && $objectTypeModule=="multimedia"}
		{assign_associative var="bkgparams" URLonly=true}
		<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
		{else}
			{if !empty($objRelated.thumbnail) && $objectType=="video"}
			{assign_associative var="bkgparams" URLonly=true}
			<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated, $bkgparams)}">
			{/if}
		{/if}
			
		{if !empty($rel)}
		<input type="hidden" class="mod" name="data[RelatedObject][{$rel}][{$objRelated.id}][modified]" value="0" />
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority {$objectTypeModule|default:''}" 
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>

		{else}
		<input style="margin-top: 0px; margin-right: 4px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objRelated.id}" value="{$objRelated.id}" />
		<input type="hidden" class="id" name="reorder[{$objRelated.id}][id]" value="{$objRelated.id}" />
		<input type="text" class="priority {$objectTypeModule}" name="reorder[{$objRelated.id}][priority]" value="{$objRelated.priority|default:""}" />
		{/if}
		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
	</td>

	<td class="filethumb">
	{if !empty($objRelated.uri) && $objectTypeModule=="multimedia"}
		{assign_associative var="params" presentation="thumb" width='155'}
		{$beEmbedMedia->object($objRelated,$params)}
	{/if}
	</td>

	<td class="assoc_obj_title"{if !empty($rel)} data-inputname="data[RelatedObject][{$rel}][{$objRelated.id}][title]"{/if}>
		<h4{if !empty($rel) && !empty($relationParamsArray)} class="editable"{/if}>{$objRelated.title|escape|default:'<i>[no title]</i>'|truncate:60:'~':true}</h4>
		<div class="show_on_more">
			{if !empty($rel) && !empty($relationParamsArray)}
			<input type="text" placeholder="{t}title{/t}" name="data[RelatedObject][{$rel}][{$objRelated.id}][title]" value="{$objRelated.title|default:''|escape}"><br>
			{/if}
			<span><label>id:</label> {$objRelated.id}</span><br>
			<span><label>nickname:</label> {$objRelated.nickname}</span><br>

			{if !empty($rel) &&  $rel == "question"}
			<span><label>type:</label> {$objRelated.question_type|default:''}</span><br>
			{/if}
		</div>
	</td> 

{if $objectTypeModule == "multimedia"}
	{$calctype = $objRelated.Category.0.name|default:$objectTypeModule} 
{else}
	{$calctype = $objectTypeModule} 
{/if}

	<td title="{$calctype}" class="obtype"><span class="icon-{$calctype}"></span></td>

	<td class="status">{$objRelated.status|default:''}</td>
	
	<td class="lang">{$objRelated.lang|default:''}</td>

	<td nowrap class="mimetype">
		{if !empty($objRelated.file_size)}{$objRelated.file_size|default:0|filesize}<br>{/if} {$objRelated.mime_type|default:''|truncate:60:'~':true}
	</td>

	<td class="moredata">
		<div class="show_on_more">
		{if !empty($rel)}
			{if !empty($relationParamsArray)}
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
				{if (in_array($objRelated.object_type_id, $conf->objectTypes['multimedia']['id']) || $objRelated.object_type_id == $conf->objectTypes['gallery']['id'])}
				<label>{t}description{/t}</label>
				<textarea name="data[RelatedObject][{$rel}][{$objRelated.id}][description]">{$objRelated.description|default:''|escape}</textarea>
				{/if}
			{/if}
		{/if}
		</div>
	</td>

	<td class="commands">
		{if !empty($objRelated.uri)}
		{if (substr($objRelated.uri,0,7) == 'http://') or (substr($objRelated.uri,0,8) == 'https://')}
	        {assign var="uri" value=$objRelated.uri}
	    {else}
	        {assign_concat var="uri" 1=$conf->mediaUrl 2=$objRelated.uri}
	    {/if}
		<a class="BEbutton" href="{$uri}" target="_blank" >{t}view file{/t}</a>
		{/if}
		<a class="BEbutton showmore">+</a>
		<a class="BEbutton golink" target="_blank" title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$objectTypeModule}/view/{$objRelated.id}"></a>	
		
		{if ($objectType != 'section')}
		<a class="BEbutton remove">x</a>
		{/if}

	</td>
</tr>
{/foreach}
{/strip}