<script type="text/javascript">
'use strict';
var refreshUrl = "{$html->here}";
var urlDelete = "{$html->url('deleteCategories/')}",
    messages = {
        'bulkDelete': "{t}Are you sure that you want to delete the selected item(s)?{/t}",
        'bulkMerge': "{t}Are you sure that you want to merge the selected item(s)? All contents related to one or more of the checked categories will be associated to the first category created within the selection, while other ones will be deleted.{/t}",
    };

$(document).ready(function() {
    $("#pageDim").bind("change", function() {
        if (refreshUrl.indexOf('/limit') > 0) {
            refreshUrl = refreshUrl.substring(0,refreshUrl.indexOf('limit')-1);
        }
        document.location = refreshUrl + "/limit:" + this.value;
    } );

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
                var htmlMsg = data.htmlMsg || null;
                if (!htmlMsg) {
                    htmlMsg = '<div class="message info">' +
                        '<h2>Info</h2>' +
                        '<p style="margin-top: 10px">' + data.info + '</p>' +
                        '<hr />' +
                        '<a class="closemessage" href="javascript:void(0)">{t}close{/t}</a>' +
                    '</div>';
                }

                $('#messagesDiv').empty()
                    .html(htmlMsg)
                    .triggerMessage('info', -1);
            });
    });

    $('input.js-label[type=text]').bind('keyup', function() {
        $(this).parent().siblings().find('input[type=submit]').prop('disabled', jQuery.trim($(this).val()) == '');
    });
});
</script>

	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
    <table>
        <tr>
            <td>
                <span class="evidence">{$pagParams.count}&nbsp;</span> {t}categories{/t}
                {assign var='label_page' value=$tr->t('Page',true)}
            </td>
            <td>
                {if $paginator->hasPrev()}
                    {$paginator->first($label_page)}
                {else}
                    {t}page{/t}
                {/if} 
                <span class="evidence"> {$paginator->current()}</span>
                {t}of{/t} 
                <span class="evidence"> 
                {if $paginator->hasNext()}
                    {$paginator->last($pagParams.pageCount)}
                {else}
                    {$paginator->current()}
                {/if}
                </span>
            </td>
            {assign var='label_next' value=$tr->t('next',true)}
            {assign var='label_prev' value=$tr->t('prev',true)}
            <td>{$paginator->next($label_next,null,$label_next,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
            <td>{$paginator->prev($label_prev,null,$label_prev,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
            <td>
                {t}Page size{/t}:
                <select name="dim" id="pageDim">
                    <option value="5"{if $pagParams.options.limit == 5} selected="selected"{/if}>5</option>
                    <option value="10"{if $pagParams.options.limit == 10} selected="selected"{/if}>10</option>
                    <option value="20"{if $pagParams.options.limit == 20} selected="selected"{/if}>20</option>
                    <option value="50"{if $pagParams.options.limit == 50} selected="selected"{/if}>50</option>
                    <option value="100"{if $pagParams.options.limit == 100} selected="selected"{/if}>100</option>
                </select>
            </td>
        </tr>
    </table>

    {assign var='label_id' value=$tr->t('id',true)}
    {assign var='label_label' value=$tr->t('label',true)}
    {assign var='label_nickname' value=$tr->t('unique name',true)}
    {assign var='label_status' value=$tr->t('status',true)}
    {assign var='label_publication' value=$tr->t('publication',true)}

    <table class="indexlist js-header-float">
        <thead>
            <tr>
                <th><input type="checkbox" class="selectAll" /></th>
                <th>{$paginator->sort($label_label,'label')}</th>
                <th>{$paginator->sort($label_nickname,'unique name')}</th>
                <th>{$paginator->sort($label_status,'status')}</th>
                <th>{$paginator->sort($label_publication,'publication')}</th>
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
