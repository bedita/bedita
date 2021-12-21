{$view->element('modulesmenu', ['substringSearch' => false])}

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

<div class="head">
    <div class="toolbar" style="white-space:nowrap">
        {$toolbarOptions = ['headerName' => 'System users', 'newAction' => 'viewUser']}
        {include file="./inc/toolbar.tpl" toolbarOptions=$toolbarOptions}
    </div>
</div>

<div class="mainfull">

    {include file = './inc/list_users.tpl' method = 'index'}

</div>