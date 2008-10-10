

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
			<span class="evidence">{$pending}&nbsp;</span> {t}pending{/t}
			</li>
			<li><span class="evidence"> {$scheduled} &nbsp;</span> {t}scheduled{/t}</li>
			
			<li> {t}next invoice at{/t}: <span class="evidence">{$nextInvoiceDate|default:$conf->dateTimePattern}</span></li>
		</ul>
		
	</div>

</div>

<div class="mainfull">

	{include file="inc/list_invoices.tpl"}
	

</div>

