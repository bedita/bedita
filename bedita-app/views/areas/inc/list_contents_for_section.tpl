{* included in list_content.tpl and used in add content in section by ajax*}
{foreach from=$objsRelated|default:'' item="c"}
	<tr class="obj {$c.status}">
		<td style="width:25px">
			<input type="hidden" class="id" name="reorder[{$c.id}][id]" value="{$c.id}" />
			<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" 
			style="width:25px"
			size="3" maxlength="3"/>
		</td>
		<td style="padding:0px; padding-top:7px; width:10px"><span title="{$conf->objectTypes[$c.object_type_id].module_name}" class="listrecent {$conf->objectTypes[$c.object_type_id].module_name}" style="margin:0px"></span></td>
		<td>
			{$c.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
			<div class="description" style="width:auto" id="desc_{$c.id}">
				{$c.description|strip_tags} / id:{$c.id} / nickname: {$c.nickname}
			</div>
		</td>
		<td>
			<a href="javascript:void(0)" onclick="$('#desc_{$c.id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span></a>	
		</td>
		<td style="white-space:nowrap">
			{$c.modified|date_format:$conf->dateTimePattern}
		</td>
		<td>
			{$c.status}
		</td>
		<td>
			{$c.lang}
		</td>
		<td class="commands" style="white-space:nowrap">
			<input type="button" class="BEbutton golink" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}{$conf->objectTypes[$c.object_type_id].module_name}/view/{$c.id}" name="details" value="››" />
			{if !empty($c.fixed)}
				
				<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-left:10px; height:12px;" />
				
			{else}
				<input type="button" name="remove" value="x" />
			{/if}
		</td>
	</tr>
{/foreach}





