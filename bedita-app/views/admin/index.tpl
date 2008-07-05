
<script type="text/javascript">
<!--
{literal}
	function viewUser(objectid) {
		document.location = "{/literal}{$html->url('/admin/viewUser')}{literal}/" + objectid;
	}
	function delUserDialog(userid,objectid) {
		if(!confirm("{/literal}{t}Do you really want to remove user{/t}{literal} " + userid + "?")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('/admin/removeUser')}{literal}/" + objectid;
	}
{/literal}
//-->
</script>

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{* da correggere e riabilitare *}
{*include file="../common_inc/toolbar.tpl"*}

<div class="mainfull">

	{include file="inc/list_users.tpl" method="index"}
	
</div>
