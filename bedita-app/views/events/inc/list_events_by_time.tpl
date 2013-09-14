<!-- https://github.com/bedita/bedita/issues/213?source=cc -->



<ul style="border-top:0px solid #666; overflow:auto">
{foreach from=$objects item=object key=key}
	{$dateprev = $date|default:''}
	{$date = $object.start_date|default:$object.created|date_format:"%a %d %B %Y"}
	{$now = $smarty.now|date_format:"%a %d %B %Y"}

	{if $date != $dateprev}
		<li class="graced" style="clear:left; background-color:{if $date==$now}#0099CC{else}gray{/if}; color:#FFF; font-size:2em; line-height:1.175em; padding:5px; border:0px solid gray; margin:0 10px 10px 0; display:block; width:118px; height:118px; float:left">
			{$date}
		</li>
	{/if}
	<li style="
	{if $object.status != "on"}opacity:.5;{else}box-shadow:0px 0px 10px rgba(0,0,0,.2); {/if}
	background-color:#fff; border:0px solid gray; margin:0 10px 10px 0; display:block; width:128px; height:128px; float:left;
	">
		{$time = $object.modified|date_format:"%H:%M"}
		<!--
		<time>{$date}</time>
		<time>{$dateprev}</time>
		-->
		<time style="padding:2px 5px 2px 5px; display:block; background-color:#CCC;">ore {$time}</time>
		<h3 style="padding:5px;">
			<a href="{$html->url('view/')}{$object.id}">{$object.title|truncate:64|default:"<i>[no title]</i>"}</a>
		</h3>
		
	</li>
		{* <!--}
		<tr class="obj {$objects[i].status}">
			<td class="checklist">
			{if !empty($objects[i].start_date) && ($objects[i].start_date|date_format:"%Y%m%d") > ($smarty.now|date_format:"%Y%m%d")}
			
				<img title="{t}object scheduled in the future{/t}" src="{$html->webroot}img/iconFuture.png" style="height:28px; vertical-align:top;">
			
			{elseif !empty($objects[i].end_date) && ($objects[i].end_date|date_format:"%Y%m%d") < ($smarty.now|date_format:"%Y%m%d")}
			
				<img title="{t}object expired{/t}" src="{$html->webroot}img/iconPast.png" style="height:28px; vertical-align:top;">
			
			{elseif (!empty($objects[i].start_date) && (($objects[i].start_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d"))) or ( !empty($objects[i].end_date) && (($objects[i].end_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d")))}
			
				<img title="{t}object scheduled today{/t}" src="{$html->webroot}img/iconToday.png" style="height:28px; vertical-align:top;">

			{/if}
			
			{if !empty($objects[i].num_of_permission)}
				<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; vertical-align:top;">
			{/if}
			
			{if (empty($objects[i].fixed))}
				<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{else}
				<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
			{/if}


			</td>
			<td style="min-width:300px">
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a>
				<div class="description" id="desc_{$objects[i].id}">
					nickname:{$objects[i].nickname}<br />
					{$objects[i].description}
				</div>
			</td>
			<td class="checklist detail" style="text-align:left; padding-top:4px;">
				<a href="javascript:void(0)" onclick="$('#desc_{$objects[i].id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span>			
				&nbsp;
				{$objects[i].id}
				</a>	
			</td>
			<td style="text-align:center">{$objects[i].status}</td>
			<td>{$objects[i].modified|date_format:$conf->dateTimePattern}</td>
			<td style="text-align:center">{$objects[i].num_of_comment|default:0}</td>
			<td>{$objects[i].lang}</td>
			<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
		</tr>
		--> *} 
		
{/foreach}
</ul>