<style scoped>
	.datelabel {
		clear:left;
		background-color:#0099CC;
		color:#FFF; 
		font-size:1.275em; 
		line-height:1.8em; 
		padding:5px; border:0px solid gray; 
		margin:0 10px 10px 0; 
		display:block; width:118px; 
		height:118px; 
		float:left;
	}

	.datelabel.on {
		background-color:#0099CC; 
	}

	.eventitem {
		/*box-shadow:0px 0px 10px rgba(0,0,0,.2);*/
		background-color:#fff; 
		border:0px solid gray; 
		margin:0 10px 10px 0; 
		display:block; 
		width:128px; 
		height:128px; 
		float:left;
	}

	.eventitem.draft, .eventitem.off {
		opacity:.5;
	}

	.eventitem time.hour {
		padding:2px 5px 2px 5px; display:block;
		background-color:#CCC;
		font-weight: normal;
	}

	.datelabel .day {
		font-size:2em;
		display:blocK;
	}

</style>

</head>

<body>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_events_by_time.tpl"}

</div>