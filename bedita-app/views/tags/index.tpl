{$html->script("form", false)}

<script type="text/javascript">
<!--
var urls = {};
urls['deleteSelected'] = "{$html->url('deleteSelected/')}";
urls['URLBase'] = "{$html->url('index/')}";
urls['urlAddMultipleTags'] = "{$html->url('addMultipleTags/')}";
urls['changestatusSelected'] = "{$html->url('changeStatus/')}";
var message = "{t}Are you sure that you want to delete the tag?{/t}";
var messageSelected = "{t}Are you sure that you want to delete selected tags?{/t}";
var no_items_checked_msg = "{t}No items selected{/t}";
//-->
</script>

{$html->script('fragments/list_objects.js', false)}

{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{include file="inc/toolbar.tpl"}

<div class="main">

<form method="post" action="" id="formObject">
	{$beForm->csrf()}

	<table class="indexlist js-header-float">

	<thead>
		<tr>
			<th></th>
			<th >
				<a href="{$html->url('/tags/index/')}label/{if $order == "label"}{$dir}{else}1{/if}">{t}Name{/t}</a></th>
			<th><a href="{$html->url('/tags/index/')}status/{if $order == "status"}{$dir}{else}1{/if}">{t}Status{/t}</a></th>
			<th><a href="{$html->url('/tags/index/')}weight/{if $order == "weight"}{$dir}{else}1{/if}">{t}Occurrences{/t}</a></th>
			<th>Id</th>
			<th>
				<img class="tagToolbar viewcloud" src="{$html->webroot}img/iconML-cloud.png" />
				<img class="tagToolbar viewlist" src="{$html->webroot}img/iconML-list.png" />
			</th>
		</tr>
	</thead>
	<tbody id="taglist">
	{foreach from=$tags item=tag}
		<tr class="obj {$tag.status}">
			<td class="checklist">
				<input type="checkbox" name="tags_selected[{$tag.id}]" class="objectCheck" title="{$tag.id}" value="{$tag.id}"/>
			</td>
			<td>
				<a href="{$html->url('view/')}{$tag.id}">{$tag.label|escape}</a>

			</td>
			<td>{$tag.status}</td>
			<td class="center">{$tag.weight}</td>
			<td><a href="{$html->url('view/')}{$tag.id}">{$tag.id}</a></td>
			<td><a href="{$html->url('view/')}{$tag.id}">{t}details{/t}</a></td>
		</tr>
	{foreachelse}

		<tr><td colspan="100" style="padding:30px">{t}No items found{/t}</td></tr>

	{/foreach}
	</tbody>

	<tbody id="tagcloud">
		<tr>
			<td colspan="10" class="tag graced" style="text-align:justify; line-height:1.5em; padding:20px 10px;">
				{foreach from=$tags item=tag}
				<span class="obj {$tag.status}">
					<a title="{$tag.weight}" class="{$tag.class|default:""}" href="{$html->url('view/')}{$tag.id}">
						{$tag.label|escape}
					</a>
				</span>
				{/foreach}
			</td>
		</tr>

	</tbody>

	</table>

	<br />

	{assign_associative var="params" bulk_tags=true objects=$tags}
	{$view->element('list_objects_bulk', $params)}

</form>

</div>
