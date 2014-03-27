<div class="quickitem">


	<form action="{$html->url('/quickitem/save')}" method="post">
		<table style="width:100%">
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
				<td><label>{t}Title{/t}</label></td>
				<td style="width:100%"><input style="width:100%" type="text" name="data[title]" /></td>
			</tr>
			<!-- TODO only if image object-type is selected -->
			<tr class="dragfile">
				<td colspan="5" style="padding:10px">
					<div style="padding-top:25px; text-align:center; border-radius:25px; font-size:16px; color:gray; border:2px dashed gray; width:100%; height:150px">
							drag images here
							<input type="file" name="file" />

					</div>
				</td>
			</tr>
			<tr>
				<td><label>{t}Description{/t}</label></td>
				<td colspan="4"><textarea style="width:100%" name="data[description]"></textarea></td>
			</tr>
			<tr>
				<td><label>{t}Position{/t}</label>
				<td colspan="4">
					<select style="max-width:440px">
					{$beTree->option($tree)}
					</select>
				</td>
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