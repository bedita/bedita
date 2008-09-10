


<div class="tab"><h2>{t}card{/t}</h2></div>

<fieldset id="card">

<ul class="htab">
	<li rel="person"><input type="radio" name="kind"> Person</li>
	<li rel="company"><input type="radio" name="kind"> Company</li>
	
</ul>

<div class="htabcontainer" id="companyperson">
	
	<div class="htabcontent" id="person" >
		<table>
			<tr>
				<th>{t}name{/t}:</th>
				<td><input type="text" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>
			</tr>	
			<tr>			
				<th>{t}surname{/t}:</th>
				<td><input type="text" name="data[surname]" value="{$object.surname|escape:'html'|escape:'quotes'}" /></td>
				<th>{t}title{/t}:</th>
				<td>
					<input type="text" style="width:45px" id="vtitle" name="data[vtitle]" value="{$object.vtitle|escape:'html'|escape:'quotes'}" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3">
					<input type="radio" name="data[sex]" value="male" /> {t}male{/t} &nbsp&nbsp
					<input type="radio" name="data[sex]" value="female" /> {t}female{/t} &nbsp&nbsp
					<input type="radio" name="data[sex]" value="transgender" /> {t}transgender{/t}
					<input type="radio" name="data[sex]" value="drone" /> {t}drone{/t} 
				</td>
			</tr>
			<tr>
				<th>{t}birthdate{/t}:</th>
				<td><input type="text" name="data[birthdate]" value="{$object.birthdate|escape:'html'|escape:'quotes'}" /></td>
			</tr>	
			<tr>
				<th>{t}deathdate{/t}:</th>
				<td><input type="text" name="data[birthdate]" value="{$object.deathdate|escape:'html'|escape:'quotes'}" /></td>
		
			</tr>
		</table>
	</div>



	<div class="htabcontent" id="company" >
		<table>
			<tr>
				<th>{t}company name{/t}:</th>
				<td><input type="text" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>
			</tr>
			<tr>
				<th>{t}company reference:{/t}</th>
			</tr>
			<tr>
				<th>{t}name{/t}:</th>
				<td><input type="text" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>		
			</tr>
			<tr>	
				<th>{t}surname{/t}:</th>
				<td><input type="text" name="data[surname]" value="{$object.surname|escape:'html'|escape:'quotes'}" /></td>
			</tr>
			<tr>
				<th>{t}title{/t}:</th>
				<td>
					<input type="text" style="width:45px" id="vtitle" name="data[vtitle]" value="{$object.vtitle|escape:'html'|escape:'quotes'}" />
				</td>
			</tr>
		</table>
	</div>
		
</div>


<em>
	Nel caso di person il title dell'oggetto è desunto accoppiando "nome + cognome"
	<br />
	Nel caso di company il title dell'oggetto è il campo "company name", mentre nome e cognome sono quelli del contatto d'azienda
	
</em>


</fieldset>


<div class="tab"><h2>{t}address{/t}</h2></div>

<fieldset id="address">
<table>
	<tr>
		<th>{t}street name and #{/t}</th>
		<td>
			<input style="width:240px;" type="text" name="data[street]" value="{$object.street|escape:'html'|escape:'quotes'}" />
			<input style="width:30px;" type="text" name="data[number]" value="{$object.number|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
	<tr>
		<th>{t}city and zip code{/t}</th>
		<td>
			<input type="text" name="data[city]" value="{$object.city|escape:'html'|escape:'quotes'}" />
			<input style="width:60px;" type="text" name="data[zip]" value="{$object.zip|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
	<tr>
		<th>{t}country{/t}</th>
		<td>
			<select type="text" name="data[country]">
				<option></option>
				<option>L'elenco ISO dei paesi doo mundo</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>{t}state{/t}</th>
		<td><input type="text" name="data[state]" value="{$object.state|escape:'html'|escape:'quotes'}" /></td>
	</tr>
</table>

</fieldset>


<div class="tab"><h2>{t}contacts{/t}</h2></div>

<fieldset id="address">
<table>
	<tr>
		<th>{t}email{/t}</th>
		<td><input type="text" name="data[email]" value="{$object.email|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}email2{/t}</th>
		<td><input type="text" name="data[email]" value="{$object.email2|escape:'html'|escape:'quotes'}" /></td>
	</tr>
	<tr>
		<th>{t}phone{/t}</th>
		<td><input type="text" name="data[phone]" value="{$object.phone|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}phone2{/t}</th>
		<td><input type="text" name="data[phone2]" value="{$object.phone2|escape:'html'|escape:'quotes'}" /></td>
	</tr>
	<tr>
		<th>{t}fax{/t}</th>
		<td><input type="text" name="data[fax]" value="{$object.fax|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}website{/t}</th>
		<td><input type="text" name="data[website]" value="{$object.website|escape:'html'|escape:'quotes'}" /></td>
	</tr>
</table>





</fieldset>
