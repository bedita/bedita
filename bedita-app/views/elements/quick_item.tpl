<div class="quickitem">
	<form action="{$html->url('/quickitem/save')}" method="post">
		<table style="width:100%">
			<tr>
				<td><label>{t}Title{/t}</label></td>
				<td colspan="4" style="width:100%"><input style="width:100%" type="text" name="data[title]" /></td>
			</tr>
			<tr>
				<td nowrap><label>{t}Object type{/t}</label></td>
				<td>
					<select>
					{assign var=leafs value=$conf->objectTypes.leafs}
					{foreach from=$conf->objectTypes item=type key=key}	
						{if ( in_array($type.id,$leafs.id) && is_numeric($key) )}
						<option {if ($type.name == 'document')}selected="selected"{/if}>	
							{t}{$type.model}{/t}
						</option>
						{/if}
					{/foreach}
					</select>
				</td>
				<td><label>{t}Position{/t}</label>
				<td colspan="4" style="width:100%" >
					<select style="max-width:440px">
					{$beTree->option($tree)}
					</select>
				</td>
			</tr>
			<!-- TODO only if image object-type is selected -->
			<tr>
				<td colspan="5">
					<input name="file" type="file" />
				</td>
			</tr>
			<tr>
				<td><label>{t}Description{/t}</label></td>
				<td colspan="4"><textarea style="width:100%" name="data[description]"></textarea></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4">
					<input type="submit" value="{t}publish{/t}"/>
					&nbsp;&nbsp;<input type="submit" value="{t}save draft{/t}"/>
				</td>
			</tr>
		</table>
	</form>
</div>