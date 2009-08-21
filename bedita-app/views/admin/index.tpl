
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


<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}System users{/t}</h2>
		{include file="./inc/toolbar.tpl" label_items='users'}
	</div>
</div>



<div class="mainfull">

	{include file="inc/list_users.tpl" method="index"}
	
</div>
