
<script type="text/javascript">
<!--
{literal}
	function viewUser(objectid) {
		document.location = "{/literal}{$html->url('/admin/viewUser')}{literal}/" + objectid;
	}
	function delUserDialog(userid,objectid,related,valid) {
		if (related) {
			if (!valid) {
				alert("{/literal}{t}User cannot be removed, he/she did create or modify some contents. It's alredy blocked{/t}{literal} ");
				return false;
			}
			if(!confirm("{/literal}{t}User cannot be removed, he/she did create or modify some contents. Do you want to block{/t}{literal} " + userid + "?")) {
				return false;
			}
			document.location = "{/literal}{$html->url('/admin/blockUser')}{literal}/" + objectid;
		}else {
			if(!confirm("{/literal}{t}Do you really want to remove user{/t}{literal} " + userid + "?")) {
				return false ;
			}
			document.location = "{/literal}{$html->url('/admin/removeUser')}{literal}/" + objectid;
		}
	}
{/literal}
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
