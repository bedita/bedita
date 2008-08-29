<script language="JavaScript">
	{literal}
	$(document).ready( function ()
	{
		$('.tab H2').addClass("open") ;
		$('.mainhalf *').show() ;
	});
	{/literal}
</script>

<style>
	.bordered {
		width:100%; 
		margin-bottom:10px;
	}


</style>
</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

<div class="head">
	

		
		<h1>Overview</h1>



</div> 


<div class="mainfull" style="padding-right:0px; margin-right:0px;">
	
<div class="mainhalf">
	<div class="tab"><h2>{t}Subscribers{/t}</h2></div>
		<ul class="bordered">
			<li>Subscribed this week: <b>12</b></li>
			<li>Subscribed this month: <b>186</b></li>
			<li>Total Subscribers: <b>2078</b></li>
			<li>
				<b>View all</b> 
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<b>Import</b> 
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<b>Export</b>
			</li>
		</ul>
	
	<div class="tab"><h2>{t}Recipient Groups{/t}</h2></div>
		<table class="bordered">
			<tr>
				<th>group name</th><th>status</th><th>subscribers</th>
			</tr>
			<tr>
				<td>Iscritti da soli due</td>
				<td style="text-align:center">on</td>
				<td style="text-align:center">123</td>
			<tr>
			</tr>
				<td>Scuole darwiniane uno</td>
				<td style="text-align:center">on</td>
				<td style="text-align:center">32</td>
			<tr>
			</tr>
				<td>Gruppo due</td>
				<td style="text-align:center">off</td>
				<td style="text-align:center">123</td>
			<tr>
			</tr>
				<td>Gruppo uno</td>
				<td style="text-align:center">hidden</td>
				<td style="text-align:center">32</td>
			<tr>
			</tr>
				<td>Gruppo due</td>
				<td style="text-align:center">on</td>
				<td style="text-align:center">123</td>
			<tr>
			</tr>
				<td colspan="2" style="border-bottom:0px;">
					<b>Manage groups</b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<b>Create new group</b>
				</td>
			</tr>
		</table>


	<div class="tab"><h2>{t}Newsletters templates{/t}</h2></div>
		<ul class="bordered">
			<li>Coincidono con asdfsaf</li>
			<li>le pubblicazion i / arre macroarree</li>
			<li>Coincidono con</li>
			<li>le pubblicazion i</li>
			<li>Coincidono con</li>
			<li>le pubblicazion i</li>
		</ul>
		
</div>
	
<div class="mainhalf" style="margin-right:0px;">
	
	<div class="tab"><h2>{t}Newsletters{/t}</h2></div>
		<ul class="bordered">
			<li>Newsletters sent this month: <b>2 </b></li>
			<li>Newsletters sent this year: <b>32</b> </li>
			<li>Queued: <b>1</b> </li>		 
			<li>Total newsletters sent: <b>36</b></li>
		</ul>
	
	<div class="tab"><h2>{t}Recent newsletters {/t}</h2></div>
	
		<table class="bordered">
			<tr>
				<th>title</th>
				<th>Sent on</th>
			{*	
				<th>to recipient</th>
				<th>template</th>
			*}
			</tr>
			<tr>
				<td>tarallilal si farloccxa this week</td>
				<td><i>not yet sent</i></td>
			{*
				<td>Iscritti da soli due</td>
				<td>pubblicazione 1</td>
			*}
			</tr>
			<tr>
				<td>titolo della newsletta</td>
				<td>12 sep 2008</td>
			</tr>
			<tr>
				<td>si  this week</td>
				<td>01 sep 2008</td>
			</tr>
			<tr>	
				<td>si farloccxa this week</td>
				<td>25 aug 2008</td>
			</tr>
			<tr>
				<td>titolo della newsletta</td>
				<td>01 aug 2008</td>
			</tr>
			<tr>	
				<td colspan="3" style="border-bottom:0px;">
					<b><a href="{$html->url('/newsletter/newsletters')}">View all</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<b><a href="{$html->url('/newsletter/view')}">Create new</a></b>
				</td>
			</tr>
		</table>

	

</div>
</div>
