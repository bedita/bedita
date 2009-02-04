<div class="tab"><h2>{t}BEdita editors activity statistics{/t}</h2></div>
	<div>	

		<h2>Oggetti prodotti</h2>

		<table class="graph">
			{foreach from=$objectsForUser key="user_id" item="user"}
			<tr>
				<td class="label">{$user.realname}</td>
				<td>
				{foreach from=$user.objects key="objectType" item="num"}
					{math assign="pixel" equation="(x/y)*350" x=$num y=$maxObjectsForUser}
					<div style="width:{$pixel}px;" class="{$conf->objectTypes[$objectType].module}">&nbsp</div>
				{/foreach}
					<span class="value">{$totalObjectsForUser[$user_id]}</span>
				</td>
			</tr>
			{/foreach}
		</table>


	</div>