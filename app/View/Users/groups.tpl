{$this->Html->script("form", false)}
{$this->Html->script("jquery/jquery.form", false)}
{$this->Html->script("jquery/jquery.cmxforms", false)}
{$this->Html->script("jquery/jquery.metadata", false)}

<script type="text/javascript">
<!--

function viewGroup(objectid) {
	document.location = "{$this->Html->url('/users/viewGroup')}/" + objectid;
}
function delGroupDialog(name,objectid) {
	if(!confirm("{t}Do you really want to remove group{/t} " + name + "?")) {
		return false ;
	}
	document.location = "{$this->Html->url('/users/removeGroup')}/" + objectid;
}
//-->
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="groups"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	{include file="./inc/toolbar.tpl" label_items='groups'}
	</div>
</div>


{include file="inc/menucommands.tpl" method="groups" fixed=true}


<div class="main">

	{include file="inc/index_groups.tpl"}

</div>


{$view->element('menuright')}