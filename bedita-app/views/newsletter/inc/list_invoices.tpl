

	
	<table class="indexlist">

		<tr>
			<th>{t}sending date{/t}</th>
			<th>{t}status{/t}</th>
			<th>{t}newsletter title{/t}</th>
			<th>{t}template{/t}</th>
			<th>{t}recipients{/t}</th>
			<th>invoice id</th>
		</tr>

			
		{section name="p" loop=5}

			<tr rel="{$html->url('/newsletter/view')}">
				<td>
					11-10-2008						
				</td>
				<td>
					pending	
				</td>
				<td>
					Titolo della newsletter in coda di invio
				</td>
				<td>
					template, da cui lapubblicazine
				</td>
				<td style="text-align:center">
					424
				</td>
				<td>
					id
				</td>
			</tr>
			
			</form>
		{/section}

		
		</table>
		


	