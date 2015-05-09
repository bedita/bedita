<script type="text/javascript">
var urlDelete = "{$html->url('deleteCategories/')}";
var messages = {
    'bulkDelete': "{t}Are you sure that you want to delete the selected item(s)?{/t}",
    'bulkMerge': "{t}Are you sure that you want to merge the selected item(s)?{/t}",
};
$(document).ready(function() {
    $('#bulkDelete, #bulkMerge').bind('click', function() {
        return confirm(messages[$(this).prop('id')]);
    });

    $('form.ajaxSave').bind('submit', function(event) {
        event.preventDefault();

        var $this = $(this),
            $loader = $('<div class="loader">').addClass('loader')
                .width(25)
                .height(25)
                .css('margin', '0 auto')
                .show(),
            $btn = $this.find('.ajaxButton').hide().after($loader);

        $.ajax({
                url: $this.prop('action') || '',
                data: $this.serialize(),
                method: $this.prop('method') || 'POST',
            })
            .always(function () {
                $btn.show();
                $loader.remove();
            })
            .error(function ($xhr, txt, err) {
                alert(err);
            })
            .done(function (data) {
                if (!data.htmlMsg) {
                    data.htmlMsg = '<div class="message info">' +
                        '<h2>Info</h2>' +
                        '<p style="margin-top: 10px">' + data.info + '</p>' +
                        '<hr />' +
                        '<a class="closemessage" href="javascript:void(0)">{t}close{/t}</a>' +
                    '</div>';
                }

                $('#messagesDiv').empty()
                    .html(data.htmlMsg)
                    .triggerMessage(type, -1);
            });
    });

    $('input.js-label[type=text]').bind('keyup', function() {
        $(this).parent().siblings().find('input[type=submit]').prop('disabled', jQuery.trim($(this).val()) == '');
    });
});
</script>

    <table class="indexlist js-header-float">
        <thead>
            <tr>
                <th><input type="checkbox" class="selectAll" /></th>
                <th>{t}label{/t}</th>
                <th>{t}unique name{/t}</th>
                <th>{t}status{/t}</th>
                <th>{t}publication{/t}</th>
                <th>Id</th>
                <th>&nbsp;</th>
            </tr>
        </thead>

        <tbody>
        {foreach $categories as $cat}
			<tr>
                <td><input type="checkbox" class="objectCheck" name="data[ids][]" value="{$cat.id}" form="bulk" /></td>
				<td>
					<input type="text" style="width:220px" name="data[label]" value="{$cat.label|escape}" class="js-label" form="form_{$cat.id}" />
				</td>

				<td>
					{if in_array('administrator', $BEAuthUser.groups)}
						<input type="text" style="width:220px" name="data[name]" value="{$cat.name}" form="form_{$cat.id}" />
					{else}
						{$cat.name}
					{/if}
				</td>

				<td style="white-space:nowrap;">
					<input type="radio" name="data[status]" value="on" {if $cat.status == "on"}checked="true"{/if} form="form_{$cat.id}" />on
					&nbsp;
					<input type="radio" name="data[status]" value="off" {if $cat.status == "off"}checked="true"{/if} form="form_{$cat.id}" />off
				</td>
				<td>
					<select style="width:180px" name="data[area_id]"form="form_{$cat.id}" >
						<option value="">--</option>
						{foreach $areasList as $area_id => $public_name}
							<option value="{$area_id}"{if $area_id == $cat.area_id} selected{/if}>{$public_name|escape}</option>
						{/foreach}
					</select>
				</td>
				<td>{$cat.id}</td>
				<td style="white-space:nowrap; ">
                    <form id="form_{$cat.id}" method="post" action="{$html->url('saveCategories')}" class="ajaxSave">
                    {$beForm->csrf()}
					<input type="hidden" name="data[id]" value="{$cat.id}" />
					<input type="hidden" name="data[object_type_id]" value="{$object_type_id}" />
					<input type="submit" value=" {t}save{/t} " class="ajaxButton" />
                    </form>
                </td>
            </tr>
        {foreachelse}
            <tr><td colspan="5">{t}No categories found{/t}</td></tr>
        {/foreach}
        </tbody>
    </table>

<br />

<div class="tab"><h2>{t 1='<span class="selecteditems">0</span>' escape=no}Operations on %1 selected items{/t}</h2></div>
<form method="post" id="bulk" action="{$html->url('bulkCategories')}">
    {$beForm->csrf()}
    <input type="submit" style="width:140px; margin-right:20px" name="data[delete]" value="{t}delete{/t}" id="bulkDelete" />
    <input type="submit" style="width:140px;" name="data[merge]" value="{t}merge{/t}" id="bulkMerge" />
</form>

<br />

<div class="tab"><h2>{t}Add new category{/t}</h2></div>
<form method="post" id="addCat" action="{$html->url('saveCategories')}">
        {$beForm->csrf()}
        <table class="indexlist">
            <thead>
                <tr>
                    <th>{t}label{/t}</th>
                    <th>{t}status{/t}</th>
                    <th>{t}publication{/t}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tr>
                <td><input type="text" style="width:220px" name="data[label]" value="" class="js-label" /></td>
                <td style="white-space:nowrap; ">
                    <input type="radio" name="data[status]" value="on" checked="true"/>on
                    &nbsp;
                    <input type="radio" name="data[status]" value="off"/>off
                </td>
                <td>
                    <select style="width:180px" name="data[area_id]">
                        <option value="">--</option>
                        {foreach $areasList as $area_id => $public_name}
                        <option value="{$area_id}">{$public_name|escape}</option>
                        {/foreach}
                    </select>
                </td>
                <td style="white-space:nowrap; width:140px; text-align:right">
                    <input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
                    <input type="submit" style="width:120px" value=" {t}save{/t} " />
                </td>
            </tr>
        </table>
</form>
