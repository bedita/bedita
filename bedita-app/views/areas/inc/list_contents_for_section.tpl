{* included in list_content.tpl and used in add content in section by ajax*}
{foreach from=$objsRelated|default:'' item="objRelated"}
<tr class="obj {$objRelated.status|default:''}" data-beid="{$objRelated.id}" data-benick="{$objRelated.nickname}">

	{assign_associative var='ObjectType' name=$conf->objectTypes[$objRelated.object_type_id].module_name}

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
		
		{if (empty($objRelated.fixed))}
			<input style="margin-top: -2px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objRelated.id}" value="{$objRelated.id}" />
		{else}
			<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
		{/if}

		{if !empty($objRelated.uri) && $ObjectType.name=="image"}
		{assign_associative var="bkgparams" URLonly=true}
		<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated,$bkgparams)}">
		{else}
			{if !empty($objRelated.thumbnail) && $objRelated.ObjectType.name=="video"}
			{assign_associative var="bkgparams" URLonly=true}
			<input type="hidden" class="rel_uri" value="{$beEmbedMedia->object($objRelated, $bkgparams)}">
			{/if}
		{/if}
		
		<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
		<input type="hidden" class="id" name="reorder[{$objRelated.id}][id]" value="{$objRelated.id}" />
		<input type="text" class="priority {$ObjectType.name}" name="reorder[{$objRelated.id}][priority]" value="{$objRelated.priority|default:""}" />
		
	</td>

	<td class="filethumb">
	{if !empty($objRelated.uri)}
		{assign_associative var="params" presentation="thumb" width='155'}
		{$beEmbedMedia->object($objRelated,$params)}
	{/if}
	</td>

	<td class="assoc_obj_title">
		<h4>{$objRelated.title|default:'<i>[no title]</i>'|truncate:60:'~':true}</h4>
	</td> 
	{if !empty($ObjectType)}
		{if $ObjectType.name == "multimedia"}
			{$calctype = $objRelated.Category.0.name|default:$ObjectType.name} 
		{else}
			{$calctype = $ObjectType.name} 
		{/if}
	{/if}

	<td title="{$calctype|default:''}" class="obtype"><span class="icon-{$calctype|default:'none'}"></span></td>

	<td class="status">{$objRelated.status|default:''}</td>
	
	<td class="lang">{$objRelated.lang|default:''}</td>

	<td nowrap class="mimetype">
		{if !empty($objRelated.file_size)}{$objRelated.file_size|default:0|filesize}&nbsp;&nbsp;{/if} {$objRelated.mime_type|default:''|truncate:60:'~':true}
	</td>

	<td class="moredata"></td>

	<td class="commands" style="text-align: right">

		<a class="BEbutton golink" title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$ObjectType.name}/view/{$objRelated.id}"></a>	
		
		<a class="BEbutton remove">x</a>

	</td>
</tr>

{/foreach}
