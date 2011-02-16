{$html->script("form", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}


<script type="text/javascript">
<!--
{literal}
function viewGroup(objectid) {
	document.location = "{/literal}{$html->url('/admin/viewGroup')}{literal}/" + objectid;
}
function delGroupDialog(name,objectid) {
	if(!confirm("{/literal}{t}Do you really want to remove group{/t}{literal} " + name + "?")) {
		return false ;
	}
	document.location = "{/literal}{$html->url('/admin/removeGroup')}{literal}/" + objectid;
}
{/literal}
//-->
</script>


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="groups"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2>{t}User groups{/t}</h2>
	
	{include file="./inc/toolbar.tpl" label_items='groups'}
	</div>
</div>


{include file="inc/menucommands.tpl" method="groups" fixed=true}

<div class="main">

	{include file="inc/form_groups.tpl"}

</div>

{$view->element('menuright')}