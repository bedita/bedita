<style scoped>

	.eventitem, .datelabel {
		background-color:#fff; 
		border:0px solid gray; 
		margin:0 10px 10px 0; 
		display:block; 
		width:128px; 
		height:128px; 
		float:left;
	}

	.datelabel {
		background-color: #0099CC;
		color: #FFF;
		font-size: 1.275em;
		line-height: 1.1;
		text-align: right;
		width: 118px;
		height: 118px;
		padding:5px;
	}

	.eventitem.draft, .eventitem.off {
		opacity:.5;
	}

	.eventitem:hover {
		background-color: #CCC;	
	}
	.eventitem time.hour {
		padding:2px 5px 2px 5px; display:block;
		color:#0099CC;
		font-weight: normal;
	}

	.datelabel .day {
		font-size:2em;
		display:blocK;
	}

	.datelabel .year {
		display:blocK;
	}

</style>

</head>

<body>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=1}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_events_by_time.tpl"}

</div>