<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var urls = {};
urls['deleteSelected'] = "{$html->url('deleteSelected/')}";
urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
urls['copyItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
urls['moveItemsSelectedToAreaSection'] = "{$html->url('moveItemsToAreaSection/')}";
urls['removeFromAreaSection'] = "{$html->url('removeItemsFromAreaSection/')}";
var no_items_checked_msg = "{t}No items selected{/t}";

//-->
</script>

{$html->script('fragments/list_objects.js', false)}

<style>
	.vlist { display:none }
</style>


<form method="post" action="" id="formObject">
	{$beForm->csrf()}
	<div id="viewthumb">
	<table class="indexlist">
	{capture name="theader"}
	<thead>
		<tr>
			<th colspan="2" nowrap>
				{*
				<img class="multimediaitemToolbar viewlist" src="{$html->webroot}img/iconML-list.png" />
				<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
				<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />
				*}
				 {t}order by{/t}:
			</th>
			<th>{$beToolbar->order('id','id')}</th>
			<th>{$beToolbar->order('title','Title')}</th>
			<th>{$beToolbar->order('name','Name')}</th>
			<th>{$beToolbar->order('mediatype','type')}</th>
			<th>{t}size{/t}</th>
			<th>{$beToolbar->order('status','Status')}</th>
			<th>{$beToolbar->order('modified','modified')}</th>
		</tr>
	</thead>
	{/capture}

		{$smarty.capture.theader}

	</table>

	<br style="clear:both" />

	{strip}
		{foreach from=$objects item="item"}
			<div class="multimediaitem itemBox{if $item.status != "on"} off{/if}">
				{assign_associative var="params" item=$item}
				{$view->element('file_item',$params)}

				<table border=0 padding="0" spacing="0" style="width:100%">
				<tr>
					<td colspan=4 class="vlist">{$item.id}</td>
					<td colspan=4 class="vlist"><a href="{$html->url('view/')}{$item.id}">{$item.title|escape}</a></td>
					<td colspan=4 class="vlist">{$item.name}</td>
					<td colspan=4 class="vlist">{$item.mediatype}</td>
					<td colspan=4 class="vlist">{math equation="x/y" x=$item.file_size|default:0 y=1024 format="%d"|default:""} KB</td>
					<td colspan=4 class="vlist">{$item.status}</td>
					<td colspan=4 class="vlist">{$item.created|date_format:'%b %e, %Y'}</td>
				</tr>
				<tr>
					{if (empty($item.fixed))}
					<td style="text-align:left; padding:0px">
						<input type="checkbox" style="width:15px" name="objects_selected[]" class="objectCheck" title="{$item.id}" value="{$item.id}" />
					</td>
					{/if}
					<td style="text-align:center; width:24px; padding:0px">
						{if !empty($item.num_of_permission)}
							<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="margin:0; width:24px; vertical-align:middle;">
						{/if}
					</td>
					<td style="text-align:center; width:24px; padding:0px">
						{if !empty($item.num_of_editor_note)}
							<img title="{$item.num_of_editor_note} {t}notes{/t}" src="/img/iconNotes.gif" style="margin:0; width:16px; vertical-align:middle;">
						{/if}
					</td>
					<td style="text-align:center; width:24px; padding:0px">
						{$media_ubiquity = ($item.num_of_relations_attach|default:0)+($item.num_of_relations_see_also|default:0)+($item.num_of_relations_download|default:0)+($item.ubiquity)}
						{if $media_ubiquity > 1}
							<img title="{t}ubiquous object{/t}" src="{$html->webroot}img/iconUbiquity.png" style="margin:0; width:18px; vertical-align:middle;">
						{/if}
					</td>

					<td style="text-align:right;"><a href="{$html->url('view/')}{$item.id}" class="BEbutton">open</a></td>


				</tr>
				</table>

			</div>
		{/foreach}
	</div>
	{/strip}

	<br style="margin:0px; line-height:0px; clear:both" />

{assign_associative var="params" bulk_tree=true bulk_categories=true}
{$view->element('list_objects_bulk', $params)}

</form>
