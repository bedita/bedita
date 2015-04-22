<script type="text/javascript">
<!--
var urls = Array();
	urls['deleteSelected'] = "{$html->url('deleteSelected/')}";
	urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
	urls['copyItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
	urls['moveItemsSelectedToAreaSection'] = "{$html->url('moveItemsToAreaSection/')}";
	urls['removeFromAreaSection'] = "{$html->url('removeItemsFromAreaSection/')}";
	urls['assocObjectsCategory'] = "{$html->url('assocCategory/')}";
	urls['disassocObjectsCategory'] = "{$html->url('disassocCategory/')}";
	urls['addToMailgroup'] = "{$html->url('addToMailgroup/')}";
    urls['exportCsv'] = "{$html->url('exportToFile/csv')}";
    urls['exportVcard'] = "{$html->url('exportToFile/vcard')}";

var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var no_items_checked_msg = "{t}No items selected{/t}";
var sel_status_msg = "{t}Select a status{/t}";
var sel_category_msg = "{t}Select a category{/t}";
var sel_copy_to_msg = "{t}Select a destination to 'copy to'{/t}";
var sel_mailgroup_msg = "{t}Select a mailgroup{/t}";

$(document).ready(function(){

    $("#assocObjectsMailgroup").click(function(e) {
		e.preventDefault();
		var mailgroup = $('#objMailgroupAssoc').val();
		if (count_check_selected() < 1) {
			alert(no_items_checked_msg);
			return false;
		}
		if (mailgroup == "") {
			alert(sel_mailgroup_msg);
			return false;
		}
		if (mailgroup != '') {
			$("#formObject").prop("action", urls['addToMailgroup']) ;
			$("#formObject").submit() ;
		}
	});
	
    $('#exportCsv').click(function(e) {
        e.preventDefault();
        $('#formObject').prop('action', urls['exportCsv']) ;
        $('#formObject').submit() ;
    });

    $('#exportVcard').click(function(e) {
        e.preventDefault();
        $('#formObject').prop('action', urls['exportVcard']) ;
        $('#formObject').submit() ;
    });

});
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
			<th>{$beToolbar->order('title','name')}&nbsp;&nbsp;&nbsp;&nbsp;{$beToolbar->order('surname','surname')}</th>
			<th>{$beToolbar->order('id','id')}</th>
			<th>{$beToolbar->order('company_name','organization')}</th>
			<th>{$beToolbar->order('status','Status')}</th>
			<th>{$beToolbar->order('modified','modified')}</th>
			<th>{t}is user{/t}</th>
			<th>{$beToolbar->order('email','email')}</th>
			{*<th>{$beToolbar->order('country','country')}</th>*}
			{if !empty($properties)}
				{foreach $properties as $p}
					<th>{$p.name}</th>
				{/foreach}
			{/if}
			<th>{$beToolbar->order('note','Notes')}</th>
		</tr>
	</thead>
	{/capture}

		{$smarty.capture.theader}

		{section name="i" loop=$objects}

		<tr class="obj {$objects[i].status}">
			<td class="checklist">
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}"/>
			</td>

			<td style="min-width:200px">
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|escape|truncate:64|default:"<i>[no title]</i>"}</a>
				<div class="description" id="desc_{$objects[i].id}">
					nickname:{$objects[i].nickname}<br />
					{$objects[i].description|escape}
				</div>
			</td>
			<td class="checklist detail" style="text-align:left;">
				<a href="javascript:void(0)" onclick="$('#desc_{$objects[i].id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
				<span class="plusminus">+</span>
				&nbsp;
				{$objects[i].id}
				</a>
			</td>

			<td>{$objects[i].company_name|default:''|escape}</td>
			<td style="text-align:center">{$objects[i].status}</td>
			<td>{$objects[i].modified|date_format:$conf->dateTimePattern}</td>
			<td style="text-align:center">{if empty($objects[i].obj_userid)}{t}no{/t}{else}{t}yes{/t}{/if}</td>
			<td>{$objects[i].email|default:''}</td>
			{* <td>{$objects[i].country}</td> *}
			{if !empty($properties)}
				{foreach $properties as $p}
					<td class="custom-property-cell">
                        <p>
					    {if !empty($objects[i].customProperties[$p.name]) && $p.object_type_id == $objects[i].object_type_id}
						    {if is_array($objects[i].customProperties[$p.name])}
							    {$objects[i].customProperties[$p.name]|@implode:", "|truncate:80:"..."|escape}
						    {else}
							    {$objects[i].customProperties[$p.name]|truncate:80:"..."|escape}
						    {/if}
					    {else}
						    -
					    {/if}
                        </p>
					</td>
				{/foreach}
			{/if}
			<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
		</tr>



		{sectionelse}

			<tr><td colspan="100" style="padding:30px">{t}No items found{/t}</td></tr>

		{/section}

</table>

<br />

{assign_associative var="params" bulk_tree=true bulk_categories=true}
{$view->element('list_objects_bulk', $params)}


{if !empty($mailgroups) && !empty($moduleList.newsletter)}

<div class="tab">
	<h2>{t}Newsletter association for{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2>
</div>
<div>
	{t}mailgroup{/t}:
	<select id="objMailgroupAssoc" name="data[mailgroup]">
		<option value="">--</option>
		{foreach from=$mailgroups item='mailgroup' key='key'}
			<option value="{$mailgroup.id}">{$mailgroup.group_name|escape}</option>
		{/foreach}
	</select>

	<input id="assocObjectsMailgroup" type="button" value="{t}Add association{/t}" />
	{bedev} / <input id="disassocObjectsMailgroup" type="button" value="{t}Remove association{/t}" />{/bedev}
</div>
{/if}	

<div class="tab">
    <h2>{t}Export{/t} <span class="evidence">{$beToolbar->size()} </span>{t}cards{/t}</h2>
</div>
<div>
    <input id="exportCsv" type="button" value="{t}Export to CSV{/t}" />
    <input id="exportVcard" type="button" value="{t}Export to vCard{/t}" />
</div>

</form>
