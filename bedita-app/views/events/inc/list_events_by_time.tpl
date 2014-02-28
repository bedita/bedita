<!-- https://github.com/bedita/bedita/issues/213?source=cc -->

<ul style="overflow:auto; margin-top:0px; ">
{foreach from=$dateItems item=item key=key}
{if !empty($item.DateItem.start_date)}
	{$dateprev = $date|default:''}
	{$date = $item.DateItem.start_date|date_format:"%A<span class='day'>%d</span> %B <span class='year'>%Y</span>"}
	{$now = $smarty.now|date_format:"%a %d %B %Y"}

	{if $date != $dateprev}
		<li class="graced datelabel{if $date==$now} on{/if}">
			{$date}
		</li>
	{/if}
	<li class="eventitem {$item.DateItem.Event.status}">
		{$time = $item.DateItem.start_date|date_format:"%H:%M"}
		<!--
		<time>{$date}</time>
		<time>{$dateprev}</time>
		-->
		<time class="hour">ore {$time}</time>
		<h3 style="padding:5px;">
			<a href="{$html->url('view/')}{$item.DateItem.Event.id}">{$item.DateItem.Event.title|truncate:64|default:"<i>[no title]</i>"}</a>
		</h3>
		
	</li>
{/if}
{/foreach}
</ul>

{*dump var=$dateItems*}