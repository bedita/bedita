{assign_associative var="cssOptions" inline=false}
{$this->Html->css("tree", null, $cssOptions)}
{$this->Html->script("jquery.treeview", false)}
{$this->Html->script("jquery.changealert", false)}


{assign var="p" value=$this->BeToolbar->params}
{assign var="toolbarstring" value=$p.named}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_objects.tpl"}
	
</div>

