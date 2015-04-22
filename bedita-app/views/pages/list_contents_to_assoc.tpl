{* included by show_objects.tpl *}

{$html->script('libs/jquery/plugins/jquery.tablesorter.min')}

<script type="text/javascript">
<!--
$(document).ready(function() {

    var $table = $('#objtable');

    $('#contents_nav a').click(function() {
        loadObjToAssoc($(this).attr('rel'));
    });

    $table.tablesorter({ 'headers': { 0: { 'sorter': false } } });  // #605 Select all - Disable sorter on first column.
    $table.find('thead th').css('cursor', 'pointer');

    $table.find('input[type="checkbox"].objectCheck').click(function() {
		var objectId = $(this).val();
		if ($(this).prop('checked')) {
			objectsChecked.add(objectId);
		} else {
			objectsChecked.remove(objectId);
		}
		// update add button
		var addLabel = $('#addButton').val();
		addLabel = addLabel.replace(/\s\d+\sitems/, '');
		var countIds = objectsChecked.get().length;
		if (countIds) {
			addLabel += ' ' + countIds + ' items';
		}
		$('#addButton').val(addLabel);
    });
});
//-->
</script>

{if !empty($objectsToAssoc.items)}
	<table class="indexlist" id="objtable" data-context="modal">
		<thead>
			<tr>
                <th><input style="margin-top: 0px; margin-right: 4px;" type="checkbox" name="selectAll" class="selectAll" data-context="modal" title="{t}Select all{/t}" value="1" /></th>
				<th></th>
				<th>{t}title{/t}</th>
				<th></th>
				<th>{t}status{/t}</th>
				<th>{t}lang{/t}</th>
				<th>{t}type{/t} and {t}size{/t}</th>
				<th>{t}more{/t}</th>
				<th style="text-align:right">{t}commands{/t}</th>
			</tr>
		</thead>
        <tbody>
            {$params = ['presentation' => 'thumb', 'width' => 64]}
            {$view->element('form_assoc_object', ['objsRelated' => $objectsToAssoc.items, 'context' => 'modal'])}
        </tbody>
	</table>

	<div id="contents_nav" class="graced" style="text-align:center; color:#333; font-size:1.1em;  margin:25px 0px 1px 0px; background-color:#FFF; padding: 5px 10px 10px 10px;">
		{$objectsToAssoc.toolbar.size} {t}items{/t} | {t}page{/t} {$objectsToAssoc.toolbar.page} {t}of{/t} {$objectsToAssoc.toolbar.pages}

		{if $objectsToAssoc.toolbar.first}
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.first}" id="streamFirstPage" title="{t}first page{/t}">{t}first{/t}</a></span>
		{/if}

		{if $objectsToAssoc.toolbar.prev}
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.prev}" id="streamPrevPage" title="{t}previous page{/t}">{t}prev{/t}</a></span>
		{/if}

		{if $objectsToAssoc.toolbar.next}
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.next}" id="streamNextPage" title="{t}next page{/t}">{t}next{/t}</a></span>
		{/if}

		{if $objectsToAssoc.toolbar.last}
			&nbsp; | &nbsp;
			<span><a href="javascript:void(0);" rel="{$objectsToAssoc.toolbar.last}" id="streamLastPage" title="{t}last page{/t}">{t}last{/t}</a></span>
		{/if}
	</div>

{else}
	<div style="background-color:#FFF; padding:20px;">{t}No item found{/t}</div>
{/if}