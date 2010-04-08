{foreach from=$objsRelated item="s"}
	<tr class="obj {$s.status}">
		<td style="width:25px">
			<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
			<input type="text" class="priority"	name="reorder[{$s.id}][priority]" value="{$s.priority|default:""}" 
			style="width:25px"
			size="3" maxlength="3"/>
		</td>
		<td style="padding:0px; padding-top:7px; width:10px"><span class="listrecent areas" style="margin:0px"></span></td>
		<td>
			{$s.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
			<div class="description" style="width:auto" id="desc_{$s.id}">
				{$s.description|strip_tags} / id:{$s.id} / nickname: {$s.nickname}
			</div>
		</td>

	<td>
			<a href="javascript:void(0)" onclick="$('#desc_{$s.id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span></a>	
		</td>
		<td style="white-space:nowrap">
			{$s.modified|date_format:$conf->dateTimePattern}
		</td>
		<td>
			{$s.status}
		</td>
		
		<td>
			{$s.lang}
		</td>
		<td class="commands" style="white-space:nowrap">

			<input type="button" class="BEbutton golink" onClick="window.location.href = ($(this).attr('href'));" 
			href="{$html->url('/')}areas/view/{$s.id}" name="details" value="››" />
			
			{if !empty($s.fixed)}
				<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-left:10px; height:12px;" />
			{/if}
			
		</td>
	</tr>
{/foreach}





