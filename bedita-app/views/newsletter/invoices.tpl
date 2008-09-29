

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="invoices"}

{include file="inc/menucommands.tpl" method="invoices" fixed=false}

<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}Invoices{/t}</h2>
		<ul>
			<li>
			<span class="evidence">1 &nbsp;</span> {t}pending{/t}
			</li>
			<li><span class="evidence"> 13 &nbsp;</span> {t}scheduled{/t}</li>
			
			<li> next invoice at: <span class="evidence">12 october 2008 : 23.30</span></li>
		</ul>
		
	</div>

</div>

<div class="mainfull">

	{include file="inc/list_invoices.tpl"}
	

</div>

