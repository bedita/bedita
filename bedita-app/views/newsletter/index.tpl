<script type="text/javascript">
	$(document).ready( function ()
	{
		$('.tab').BEtabstoggle();
	});
</script>

<style>
	.bordered {
		width:100%; 
		margin-bottom:10px;
	}

</style>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

<div class="head">
	
		<h1>{t}Overview{/t}</h1>

</div> 

<div class="mainfull" style="padding-right:0px; margin-right:0px;">
	
<div class="mainhalf">
	<div class="tab"><h2>{t}Subscribers{/t}</h2></div>
		<ul class="bordered">
			<li>{t}Subscribed this week{/t}: <b>{$subscribedWeek|default:0}</b></li>
			<li>{t}Subscribed this month{/t}: <b>{$subscribedMonth|default:0}</b></li>
			<li>{t}Total Subscribers{/t}: <b class="evidence">{$subscribedTotal|default:0}</b></li>
			<li>
				<b><a href="{$html->url('/newsletter/mailGroups')}">{t}View all{/t}</a></b> 
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<b><a href="{$html->url('/newsletter/mailGroups')}">{t}Import{/t}</a></b> 
			</li>
		</ul>

	<div class="tab"><h2>{t}Newsletters {/t}</h2></div>
		<table class="bordered" border=0 style="margin-top:-5px; width:100%">
		{if !empty($recentMsg)}
			<tr>
				<th style="width:100%">{t}title{/t}</th>
				<th>{t}Sent on{/t}</th>
			</tr>
			
			{foreach from=$recentMsg item="msg"}
			<tr>
				<td><a href="{$html->url('/newsletter/viewMailMessage/')}{$msg.id}">{$msg.title|escape}</a></td>
				<td style="white-space:nowrap">
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
					<b><a href="{$html->url('/newsletter/newsletters')}">{t}View all{/t}</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<b><a href="{$html->url('/newsletter/viewMailMessage')}">{t}Create new{/t}</a></b>
				</td>
			</tr>
		{else}
			<tr><td colspan="2" style="width:340px;">{t}No newsletters found{/t}</td></tr>
			<tr>	
				<td colspan="2" style="border-bottom:0px;">
					<b><a href="{$html->url('/newsletter/viewMailMessage')}">{t}Create new{/t}</a></b>
				</td>
			</tr>
		{/if}

		</table>
		
</div>
	
<div class="mainhalf" style="margin-right:0px;">
	
	<div class="tab"><h2>{t}Invoices{/t}</h2></div>
		<ul class="bordered">
			<li>{t}Newsletters sent this month{/t}: <b>{$sentThisMonth|default:0} </b></li>
			<li>{t}Newsletters sent this year{/t}: <b>{$sentThisYear|default:0}</b> </li>
			<li>
				{t}Total newsletters sent{/t}: <b>{$sentTotal|default:0}</b> 
				&nbsp; &nbsp; | &nbsp; &nbsp; 
				{t}Queued{/t}: <b class="evidence">{$queued|default:0}</b> </li>		 
			<li>
				<b><a href="{$html->url('/newsletter/invoices')}">{t}View invoices{/t}</a></b>
			</li>
		</ul>
	
	<div class="tab"><h2>{t}Templates{/t}</h2></div>
		<ul class="bordered">
		{foreach from=$templates item="temp"}
			<li><a href="{$html->url('/newsletter/viewMailTemplate/')}{$temp.BEObject.id}">{$temp.BEObject.title|escape}</a></li>
		{foreachelse}
			<li>{t}No template available{/t}</li>
		{/foreach}
			<li>
				<b><a href="{$html->url('/newsletter/templates')}">{t}View all{/t}</a></b>
					&nbsp;&nbsp;|&nbsp;&nbsp;
				<b><a href="{$html->url('/newsletter/viewMailTemplate')}">{t}Create new{/t}</a></b> 
			</li>
		</ul>

</div>
</div>
