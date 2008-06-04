
<div class="tab"><h2>{t}System info {/t}</h2></div>

<div>	
	
		<ul class="bordered">
			<li><label>bedita: {$conf->Bedita.version}</label></li>
			<li><label>cake:  {$conf->Cake.version}</label></li>
		</ul>

</div>


		

<div class="tab"><h2>{t}System events{/t}</h2></div>

<div>

	<table class="indexlist">
		<tr>
			<th>{t}Date{/t}</th>
			<th>{t}Level{/t}</th>
			<th>{t}User{/t}</th>
			<th>{t}Msg{/t}</th>
			<th>{t}Context{/t}</th>
		</tr>

		{foreach from=$events item=e}
		<tr>
			<td style="white-space:nowrap">{$e.EventLog.created}</td>
			<td class="{$e.EventLog.level}">{$e.EventLog.level}</td>
			<td>{$e.EventLog.user}</td>
			<td>{$e.EventLog.msg}</td>
			<td>{$e.EventLog.context}</td>
		</tr>
		{/foreach}
	
	</table>

</div>