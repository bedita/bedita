<script type="text/javascript">
	{literal}
	$(document).ready( function ()
	{
		$('.tab').BEtabstoggle();
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
			<li>Subscribed this week: <b>{$subscribedWeek|default:0}</b></li>
			<li>Subscribed this month: <b>{$subscribedMonth|default:0}</b></li>
			<li>Total Subscribers: <b>{$subscribedTotal|default:0}</b></li>
			<li>
				<b><a href="{$html->url('/addressbook/')}">View all</a></b> 
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<b><a href="{$html->url('/addressbook/import')}">Import</a></b> 
			</li>
		</ul>


	<div class="tab"><h2>{t}Templates{/t}</h2></div>
		<ul class="bordered">
		{foreach from=$templates item="temp"}
			<li><a href="{$html->url('/newsletter/viewtemplate/')}{$temp.BEObject.id}">{$temp.BEObject.title}</a></li>
		{foreachelse}
			<li>{t}No template avaible{/t}</li>
		{/foreach}
		
		</ul>
		
</div>
	
<div class="mainhalf" style="margin-right:0px;">
	
	<div class="tab"><h2>{t}Newsletters{/t}</h2></div>
		<ul class="bordered">
			<li>Newsletters sent this month: <b>{$sentThisMonth|default:0} </b></li>
			<li>Newsletters sent this year: <b>{$sentThisYear|default:0}</b> </li>
			<li>Queued: <b>{$queued|default:0}</b> </li>		 
			<li>Total newsletters sent: <b>{$sentTotal|default:0}</b></li>
		</ul>
	
	<div class="tab"><h2>{t}Recent newsletters {/t}</h2></div>
	
		<table class="bordered">
		{if !empty($recentMsg)}
			<tr>
				<th>{t}title{/t}</th>
				<th>{t}Sent on{/t}</th>
			</tr>
			
			{foreach from=$recentMsg item="msg"}
			<tr>
				<td>{$msg.title}</td>
				<td>
				{if $msg.mail_status == "sent"}
					{$msg.start_sending|date_format:$conf->datePattern}
				{else}
					<i>{t}not yet sent{/t}</i>
				{/if}
				</td>
			</tr>
			{/foreach}
			<tr>	
				<td colspan="2" style="border-bottom:0px;">
					<b><a href="{$html->url('/newsletter/newsletters')}">View all</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<b><a href="{$html->url('/newsletter/view')}">Create new</a></b>
				</td>
			</tr>
		{else}
			<tr><td colspan="2" style="border:0;">{t}No newsletters found{/t}</td></tr>
		{/if}

		</table>

</div>
</div>
