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

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

<div class="head">
    <div class="toolbar" style="white-space:nowrap">
        {include file="./inc/toolbar.tpl" label_items='tags'}
    </div>
</div>

<div class="main">

{$view->element('filters', [
    'options' => [
        'tree' => false,
        'treeDescendants' => false,
        'relations' => false,
        'language' => false,
        'user' => false,
        'customProp' => false,
        'categories' => false,
        'tags' => false,
        'status' => false,
        'editorialContents' => false
    ]
])}

<form method="post" action="" id="formObject">
    {$beForm->csrf()}

    <table class="indexlist js-header-float">

    <thead>
        <tr>
            <th></th>
            <th>{$paginator->sort($tr->t('Name', true), 'name')}</th>
            <th>{$paginator->sort($tr->t('Status', true), 'status')}</th>
            <th>{t}Weight{/t}</th>
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

    </table>

    <br />

    {assign_associative var="params" bulk_tags=true objects=$tags}
    {$view->element('list_objects_bulk', $params)}

</form>

</div>
