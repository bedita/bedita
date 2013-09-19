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
{/foreach}
</ul>

{dump var=$dateItems}