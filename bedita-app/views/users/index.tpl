<script type="text/javascript">
<!--

	function viewUser(objectid) {
		document.location = "{$html->url('/users/viewUser')}/" + objectid;
	}
	function delUserDialog(userid,objectid) {
		if(!confirm("{t}Do you really want to remove user{/t} " + userid + "?")) {
			return false ;
		}
		document.location = "{$html->url('/users/removeUser')}/" + objectid;
	}

//-->
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}System users{/t}</h2>
		{include file="./inc/toolbar.tpl" label_items='users'}
	</div>
</div>

<div class="mainfull">

	{include file="inc/list_users.tpl" method="index"}
	
</div>