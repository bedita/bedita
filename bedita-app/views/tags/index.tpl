{$html->script("form", false)}

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteSelected/')}";
var message = "{t}Are you sure that you want to delete the tag?{/t}";
var messageSelected = "{t}Are you sure that you want to delete selected tags?{/t}";
var URLBase = "{$html->url('index/')}";
var urlAddMultipleTags = "{$html->url('addMultipleTags/')}";
var urlChangeStatus = "{$html->url('changeStatus/')}";
var no_items_checked_msg = "{t}No items selected{/t}";

function count_check_selected() {
	var checked = 0;
	$('input[type=checkbox].objectCheck').each(function(){
		if ($(this).prop("checked")) {
			checked++;
		}
	});
	return checked;
}
$(document).ready(function() {

	$("#deleteSelected").bind("click", function() {
		if (count_check_selected() < 1) {
			alert(no_items_checked_msg);
			return false;
		}
		if (!confirm(messageSelected)) {
			return false;
		}
		$("#formObject").prop("action", urlDelete);
		$("#formObject").submit();
	});
	

	$("#taglist").hide();
	
	$(".tagToolbar.viewlist").click(function () {
		$("#taglist").show();
		$("#tagcloud").hide();
	});
	$(".tagToolbar.viewcloud").click(function () {
		$("#taglist").hide();
		$("#tagcloud").show();
	});
	
	$("#addmultipletag").click(function() {
		$("#formObject").prop("action", urlAddMultipleTags) ;
		$("#formObject").submit();
	});
	
	$("#changestatusSelected").click(function() {
		if (count_check_selected() < 1) {
			alert(no_items_checked_msg);
			return false;
		}
		$("#formObject").prop("action", urlChangeStatus) ;
		$("#formObject").submit();
	});

});

//-->
</script>

{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{include file="inc/toolbar.tpl"}

<div class="main">

<form method="post" action="" id="formObject">

	<table class="indexlist">

	<tr>
		
		<th style="width:50px;">
			
			<img class="tagToolbar viewcloud" src="{$html->webroot}img/iconML-cloud.png" />
			<img class="tagToolbar viewlist" src="{$html->webroot}img/iconML-list.png" />
			
		</th>
		
		<th><a href="{$html->url('/tags/index/')}label/{if $order == "label"}{$dir}{else}1{/if}">{t}Name{/t}</a></th>
		<th><a href="{$html->url('/tags/index/')}status/{if $order == "status"}{$dir}{else}1{/if}">{t}Status{/t}</a></th>
		<th><a href="{$html->url('/tags/index/')}weight/{if $order == "weight"}{$dir}{else}1{/if}">{t}Occurrences{/t}</a></th>
		<th>Id</th>
		<th></th>
	</tr>
	<tbody id="taglist">
	{foreach from=$tags item=tag}
		<tr class="obj {$tag.status}">
			<td style="width:36px; text-align:center">
				<input type="checkbox" name="tags_selected[{$tag.id}]" class="objectCheck" title="{$tag.id}" value="{$tag.id}"/>
			</td>
			<td>
				<a href="{$html->url('view/')}{$tag.id}">{$tag.label}</a>
				
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
			<td colspan="10" class="tag graced" style="text-align:justify; line-height:1.5em; padding:20px;">
				{foreach from=$tags item=tag}
				<span class="obj {$tag.status}">
					<a title="{$tag.weight}" class="{$tag.class|default:""}" href="{$html->url('view/')}{$tag.id}">
						{$tag.label}
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