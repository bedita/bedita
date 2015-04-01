
{$view->set('method', $view->action)}

{$view->element('modulesmenu')}

{include file= './inc/menuleft.tpl'}

{include file= './inc/menucommands.tpl'}

{$view->element('toolbar', ['itemName' => 'cards'])}

<div class="mainfull">

    {$view->element('filters')}
    {include file= './inc/list_objects.tpl'}

</div>

