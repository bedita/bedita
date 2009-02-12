{$javascript->link("jquery/jquery.disable.text.select", true)}
{literal}
 <script type="text/javascript">
    $(function() {
        $('.disableSelection').disableTextSelect();
    });
    </script>
{/literal}


{foreach from=$objsRelated item="s"}

<table class="itemBox obj {$s.status}">
	<tr class="disableSelection">
	<td>
		<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
		<input type="text" class="priority"	name="reorder[{$s.id}][priority]" value="{$s.priority|default:""}" size="3" maxlength="3"/>
	</td>
	<td>
		<span title="{$s.module}" class="listrecent areas" style="margin-left:0px">&nbsp;&nbsp;</span>
	</td>
	<td style="width:100%">
		{$s.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
	</td>
	<td>
		{$s.lang}
	</td>
	<td class="commands">
		<input type="button" class="BEbutton link" onClick="window.location.href = ($(this).attr('href'));" 
		href="{$html->url('/')}areas/index/{$s.id}" name="details" value="››" />
	</td>
	</tr>
</table>

{/foreach}


{*foreach from=$sections.items item=s}
		<li class="itemBox obj {$s.status}">
			<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
			<input type="text" class="priority"  name="reorder[{$s.id}][priority]" value="{$s.priority}" size="3" maxlength="3"/>
			<span class="listrecent areas" style="margin-left:0px">&nbsp;&nbsp;</span>
			<a title="{$s.created}" href="{$html->url('/')}areas/index/{$s.id}">{$s.title|truncate:"70":"…":true}</a>
			
			<div style="margin-top:-20px; float:right;">
				{$s.lang}
			</div>
			
		</li>
		{/foreach*}



