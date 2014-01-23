
<div class="tab"><h2>{t}Referenced in{/t}</h2></div>
<fieldset id="relationships">
<table class="bordered">
	{if empty($object.relations)}
		<tr><th>{t}No references{/t}</th></tr>
	{else}
		{foreach from=$object.relations key="name" item="related"}
		<tr>
			<th colspan="10" style="padding:5px 0 5px 0 ; font-size:1.1em; font-weight:bold;">
				{$name}
			</th>
		</tr>
			{foreach from=$related item="o"}
			<tr>
				<td nowrap style="padding-left:0px">
					<span class="listrecent {$o.ObjectType.module_name}" style="vertical-align:middle; margin:0px 5px 0 0"></span>
					<a href="{$html->url('/view/')}{$o.id}">{$o.ObjectType.name}</a>
				</td>
				<td>
					<a href="{$html->url('/view/')}{$o.nickname}">{$o.title|default:'<i>[no title]</i>'|truncate:64:'…'}</a>
					{*
					<!-- {if !empty($o.customProperties)}
						<table class="customPropList">
						{foreach from=$o.customProperties item=custom key=key}
							<tr><td>{$key}</td><td>{$custom}</td></tr>
						{/foreach}
						</table>
					{/if} -->
					*}
				</td>
				<td>
					<a href="{$html->url('/view/')}{$o.id}">{$o.status}</a>
				</td>
			</tr>
			{/foreach}
		{/foreach}
	{/if}
</table>
</fieldset>
