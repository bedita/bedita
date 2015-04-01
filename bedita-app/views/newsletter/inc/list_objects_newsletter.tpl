<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var urls = Array();
urls['deleteSelected'] = "{$html->url('delete/')}";
urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
urls['copyItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
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
				<th>{$beToolbar->order('id','id')}</th>
				<th>{$beToolbar->order('status','Status')}</th>
				<th>{$beToolbar->order('sent','last invoice')}</th>
				<th>{$beToolbar->order('template','Template')}</th>
				<th>{$beToolbar->order('lang','language')}</th>
			</tr>
		</thead>
	{/capture}

		{$smarty.capture.theader}

		{section name="i" loop=$objects}

		<tr class="obj {$objects[i].mail_status}">
			<td class="checklist">
			{if (empty($objects[i].fixed))}
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{/if}
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|escape|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].id}</td>

			{if !empty($objects[i].mail_status) && $objects[i].mail_status == "injob"}
				<td style="color:red; text-decoration: blink;">{t}in job{/t}</td>
			{elseif  ($objects[i].mail_status == "pending")}
				<td class="info">{t}{$objects[i].mail_status|default:''}{/t}</td>
			{else}
				<td>{t}{$objects[i].mail_status|default:''}{/t}</td>
			{/if}


			<td>{if !empty($objects[i].sent)}{$objects[i].sent|date_format:$conf->dateTimePattern} {/if}</td>

			<td>
			{if !empty($objects[i].relations.template)}
				<a href="{$html->url('/newsletter/viewtemplate/')}{$objects[i].relations.template.0.id}">{$objects[i].relations.template.0.title}</a>
			{/if}
			</td>
			<td>{$objects[i].lang}</td>
		</tr>

		{sectionelse}

			<tr><td colspan="100">{t}No items found{/t}</td></tr>

		{/section}

</table>


<br />

{assign_associative var=params bulk_status=false}
{$view->element('list_objects_bulk', $params)}

</form>
