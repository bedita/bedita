{$html->script("form", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewGroup"}

<div class="head">
    <h1>{if !empty($group)}{t}Group{/t} "{$group.Group.name}"{else}<i>[{t}New group{/t}]</i>{/if}</h1>
</div>

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2></h2>
	</div>
</div>


{include file="inc/menucommands.tpl" method="viewGroup" fixed=true}


<div class="main">

	{include file="inc/form_group.tpl"}

</div>


{$view->element('menuright')}