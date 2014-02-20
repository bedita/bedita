<script type="text/javascript">
<!--

	function viewUser(objectid) {
		document.location = "{$html->url('/users/viewUser')}/" + objectid;
	}

	function delUserDialog(userid, objectid, related, valid) {
		if (related) {
			if (!valid) {
				alert("{t}User cannot be removed, he/she did create or modify some contents. It's alredy blocked{/t} ");
				return false;
			}
			if (!confirm("{t}User cannot be removed, he/she did create or modify some contents. Do you want to block{/t} " + userid + "?")) {
				return false;
			}
			document.location = "{$html->url('/users/blockUser')}/" + objectid;
		} else {
			if (!confirm("{t}Do you really want to remove user{/t} " + userid + "?")) {
				return false ;
			}
			document.location = "{$html->url('/users/removeUser')}/" + objectid;
		}
	}

//-->
</script>

{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		{include file="./inc/toolbar.tpl" label_items='users'}
	</div>
</div>

<div class="mainfull">

	{include file="inc/list_users.tpl" method="index"}
	
</div>