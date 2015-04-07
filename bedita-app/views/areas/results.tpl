
{$view->element('modulesmenu',['searchDestination' => 'results'])}

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

{$view->element('toolbar')}

<div class="mainfull">

    {$view->element('filters')}

    {$view->element('list_objects')}
    
</div>