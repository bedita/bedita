{$html->script("form", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}

<script type="text/javascript">
<!--

function viewGroup(objectid) {
	document.location = "{$html->url('/users/viewGroup')}/" + objectid;
}
function delGroupDialog(name,objectid) {
	if(!confirm("{t}Do you really want to remove group{/t} " + name + "?")) {
		return false ;
	}
	document.location = "{$html->url('/users/removeGroup')}/" + objectid;
}
//-->
</script>

{$view->element('modulesmenu', ['searchDestination' => $view->action, 'substringSearch' => false])}

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