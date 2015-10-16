<div class="quickitem" id="addQuickItemWrap">
	{$html->script('fragments/quick_item')}
	<form id="addQuickItem"{if isset($ajax) && $ajax==true}class="ajaxSubmit"{/if} action="{$html->url('/pages/saveQuickItem')}" method="post" enctype="multipart/form-data">
		{$beForm->csrf()}
		<table style="width:100%">
			<tr>
				<td><label>{t}Title{/t}</label></td>
				<td colspan="4" style="width:100%"><input style="width:100%" type="text" name="data[title]" /></td>
			</tr>
			<tr>
				<td nowrap><label>{t}Object type{/t}</label></td>
				<td>
					<select name="data[object_type_id]">
					{$objectTypeIds = $objectTypeIds|default:$conf->objectTypes.leafs.id}
					{foreach $objectTypeIds as $typeId}
						{strip}
						{if !empty($conf->objectTypes[$typeId])}
							<option {if ($conf->objectTypes[$typeId].name == 'document')}selected="selected"{/if} value="{$typeId}" {if in_array($typeId, $conf->objectTypes.multimedia.id)}data-multimedia="true"{/if}>
								{t}{$conf->objectTypes[$typeId].name}{/t}
							</option>
						{/if}
						{/strip}
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td><label>{t}Position{/t}</label></td>
				<td colspan="4" style="width:100%" >
					<select class="areaSectionAssociation max-width:440px" name="data[destination]">
						<option value="">{t}None{/t}</option>
						{$beTree->option($tree)}
					</select>
				</td>
			</tr>
			<tr id="quickitemFileContainer">
				<td><label>{t}File{/t}</label></td>
				<td colspan="4">
					<input name="Filedata" type="file" style="width: 100%" />
				</td>
			</tr>
			<tr>
				<td><label>{t}URL{/t}</label></td>
				<td colspan="4"><input type="url" name="data[url]" style="width: 100%" /></td>
			</tr>
			<tr>
				<td><label>{t}Description{/t}</label></td>
				<td colspan="4"><textarea style="width:100%" name="data[description]"></textarea></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4">
					<input type="hidden" name="data[status]" value=""/>
					<input type="submit" data-status="on" value="{t}publish{/t}"/>
					&nbsp;&nbsp;<input type="submit" data-status="draft" value="{t}save draft{/t}"/>
				</td>
			</tr>
		</table>
	</form>
</div>