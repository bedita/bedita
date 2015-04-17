
{$view->element('modulesmenu',['searchDestination' => 'results'])}

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

{$view->element('toolbar')}

<div class="mainfull">

    {$view->element('filters', [
        'options' => [
            'type' => true
        ]
    ])}

    {$view->element('list_objects', [
        'bulk_tree' => false,
        'bulk_delete' => false
    ])}
    
</div>