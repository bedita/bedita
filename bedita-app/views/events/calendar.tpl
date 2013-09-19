</head>

<body>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_events_by_time.tpl"}

</div>