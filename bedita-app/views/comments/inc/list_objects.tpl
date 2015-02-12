<script type="text/javascript">
<!--
var urls = {};
urls['URLBase'] = "{$html->url('index/')}" ;
urls['deleteSelected'] = "{$html->url('deleteSelected/')}" ;
urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
urls['moveItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var no_items_checked_msg = "{t}No items selected{/t}";
//-->
</script>

{$html->script('fragments/list_objects.js', false)}

<form method="post" action="" id="formObject">
	{$beForm->csrf()}
	<input type="hidden" name="data[id]"/>

	<table class="indexlist js-header-float">
	{capture name="theader"}
	<thead>
		<tr>
			<th></th>
			<th>{$beToolbar->order('title','Title')}</th>
			<th>{$beToolbar->order('ReferenceObject.title','object title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created','inserted on')}</th>
			<th>{$beToolbar->order('email','email')}</th>
			<th>{$beToolbar->order('ip_created', 'IP')}</th>
			<th>{$beToolbar->order('id','id')}</th>
		</tr>
	</thead>
	{/capture}

		{$smarty.capture.theader}

		{section name="i" loop=$objects}

		<tr class="obj {$objects[i].status}">
			<td class="checklist">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|escape|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].ReferenceObject.title|escape}</td>
			<td>{$objects[i].status}</td>
			<td>{$objects[i].created|date_format:$conf->dateTimePattern}</td>
			<td>{$objects[i].email|default:''}</td>
			<td>{$objects[i].ip_created}</td>
			<td>{$objects[i].id}</td>
		</tr>

		{sectionelse}

			<tr><td colspan="100">{t}No items found{/t}</td></tr>

		{/section}

</table>


<br />

{$view->element('list_objects_bulk')}

</form>
