{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

{*$javascript->link("jquery/jquery.MultiFile.pack", false)*}

</head>

<body>


{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

{include file="../common_inc/toolbar.tpl"}

<div class="mainfull">

	{include file="../common_inc/list_streams.tpl" method="index" streamTitle="multimedia"}
	

	<div class="tab"><h2>{t}Add multiple items{/t}</h2></div>
	<div>
		
		<input type="file" class="multi"/>
		<hr />
		<input type="submit" value="  {t}add{/t}   " />
	
	</div>


	
</div>


