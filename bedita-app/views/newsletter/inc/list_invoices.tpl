<table class="indexlist vtop">
	<tr>
		<th>{t}sending date{/t}</th>
		<th>{t}status{/t}</th>
		<th>{t}newsletter title{/t}</th>
		<th>{t}to recipients{/t}</th>
{*		<th>{t}template{/t}</th>*}
		<th>invoice id</th>
	</tr>

	{foreach from=$objects item="obj"}
		<tr>
			<td>{$obj.start_sending|date_format:$conf->dateTimePattern}</td>
			<td style="text-align:center">
				
				{if ( (!empty($obj.start_sending)) && ( $obj.start_sending < ($smarty.now+3600|date_format:"%Y-%m-%d %T") ) )}
					{assign var="status" value="pendingAlert"}
				{/if}
						
				<img alt="{$obj.mail_status}" title="{$obj.mail_status}" style="width:18px" src="/img/icon{$status|default:$obj.mail_status}.png" />
				
				{assign var="status" value=""}
			</td>
			<td><a title="details of '{$obj.title}'" href="{$html->url('/newsletter/view/')}{$obj.id}">{$obj.title}</a></td>
			<td style="padding-left:20px">
				<ul style="list-style-type:disc">
				{foreach from=$obj.MailGroup item="recipient"}
					<li><a href="{$html->url('/newsletter/view_mail_group/')}{$recipient.id}">{$recipient.group_name}</li>
				{/foreach}
				</ul>
			</td>
{*			<td>
			{if !empty($obj.relations.template)}
				{$obj.relations.template.0.title}
			{/if}
			</td>*}
			<td style="text-align:right">{$obj.id}</td>
		</tr>
	{foreachelse}
		<tr><td colspan="100">{t}No invoices{/t}</td></tr>
	{/foreach}
		
</table>
		
{*dump var=$objects*}