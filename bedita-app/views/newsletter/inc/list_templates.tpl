

	
	<table class="indexlist">

		<tr>
			<th>{t}name{/t}</th>
			<th>{t}publishing{/t}</th>
			<th>{t}sender{/t}</th>
			<th>Id</th>
		</tr>

			
		{section name="p" loop=5}

			<tr rel="{$html->url('/newsletter/viewtemplate')}">

				<td>
					Nome convenzionale del template
				</td>
				<td>
					Nome della Pubblicazione di riferimento							
				</td>
				<td>
					sender@email.be
				</td>
				<td>
					id
				</td>
			</tr>
			
			</form>
		{/section}

		
		</table>
		


	