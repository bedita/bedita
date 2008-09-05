


<div class="tab"><h2>{t}card{/t}</h2></div>

<fieldset id="personal">

<table>
	<tr>
		<th>{t}full Name{/t}</th>
		<td><input type="text" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}title{/t}</th>
		<td>
			<input type="text" id="vtitle" name="data[vtitle]" value="{$object.vtitle|escape:'html'|escape:'quotes'}" />

		</td>
	</tr>
	<tr>
		<td colspan="4"><hr /></td>
	</tr>
	<tr>
		<th>{t}name{/t}</th>
		<td><input type="text" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}surname{/t}</th>
		<td><input type="text" name="data[surname]" value="{$object.surname|escape:'html'|escape:'quotes'}" /></td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2">
			<input type="radio" name="data[sex]" value="male" /> {t}male{/t} &nbsp&nbsp
			<input type="radio" name="data[sex]" value="female" /> {t}female{/t} &nbsp&nbsp
			<input type="radio" name="data[sex]" value="transgender" /> {t}transgender{/t} 
		</td>
	</tr>
	<tr>
		<th>{t}birthdate{/t}</th>
		<td><input type="text" name="data[birthdate]" value="{$object.birthdate|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}deathdate {/t}</th>
		<td><input type="text" name="data[birthdate]" value="{$object.deathdate|escape:'html'|escape:'quotes'}" /></td>

	</tr>
</table>

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
