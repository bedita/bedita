<!-- https://github.com/bedita/bedita/issues/213?source=cc -->

<ul style="border-top:0px solid #666; overflow:auto">
{foreach from=$objects item=object key=key}
	{$dateprev = $date|default:''}
	{$date = $object.start_date|default:$object.created|date_format:"%a %d %B %Y"}
	{$now = $smarty.now|date_format:"%a %d %B %Y"}

	{if $date != $dateprev}
		<li class="graced datelabel{if $date==$now} on{/if}">
			{$date}
		</li>
	{/if}
	<li class="eventitem {$object.status}">
		{$time = $object.modified|date_format:"%H:%M"}
		<!--
		<time>{$date}</time>
		<time>{$dateprev}</time>
		-->
		<time class="hour">ore {$time}</time>
		<h3 style="padding:5px;">
			<a href="{$html->url('view/')}{$object.id}">{$object.title|truncate:64|default:"<i>[no title]</i>"}</a>
		</h3>
		
	</li>
{/foreach}
</ul>

{dump var=$dateItems}