{$view->element('texteditor')}

<script type="text/javascript">

$(document).ready(function(){

	{if (!empty($object.id) && ($object.company==1))}
		show_company();
	{else}
		show_person()
	{/if}
	

	$("TD[rel]").css("cursor","pointer").click(function () {
		$("input", this).prop('checked', true);
		var cardtype = $(this).attr('rel');
		if (cardtype == "company") {
			show_company();
		} else {
			show_person();
		}


	});
	
	{if (!empty($object) && !empty($object.country))}
	   $("#country").selectOptions("{$object.country}",true);
	{/if}
	
	function show_company(){
		$('.labelcompany').show()
		$('.labelperson').hide()
		$('#company_name').insertBefore("#titlex");
	}
	function show_person(){
		$('.labelcompany').hide()
		$('.labelperson').show()
		$('#company_name').insertAfter("#gender");
	}

});


</script>


<div class="tab"><h2>{t}Card{/t}</h2></div>

<fieldset id="card">

		<table id="companychoice">
			<tr>
				<td  rel="person">
					<input type="radio" name="data[company]" value="0" {if (empty($object.id)||($object.company==0))}checked="checked"{/if} />
					{t}Person{/t}</td>
				<td rel="company">
					<input type="radio" name="data[company]" value="1" {if (!empty($object.id)&&($object.company==1))}checked="checked"{/if} />
					{t}Organization{/t}</td>
			</tr>
		</table>
		
		<table style="margin-top:20px; width:100%;">
			
			<tr id="company_name">
				<th nowrap><span class="labelcompany">{t}company name{/t}</span><span class="labelperson">{t}Organization{/t}</span>:</th>
				<td colspan="4"><input type="text" style="width:100%" name="data[company_name]" value="{$object.company_name|escape:'html'|escape:'quotes'}" /></td>				
			</tr>
			
			<tr id="titlex">
				<th style="width:70px;">
					<span class="labelperson">{t}title{/t}</span>
					<span class="labelcompany">{t}legal form{/t}</span>:</th>
				<td colspan="4">
					<input type="text" style="width:50%" id="vtitle" name="data[person_title]" value="{$object.person_title|escape:'html'|escape:'quotes'}" />
				</td>
			</tr>
			
			<tr class="labelcompany">
				<td></td><th colspan="3"><b>{t}Person of reference{/t}</b></th>
			</tr>
			
			<tr>
				<th>{t}name{/t}:</th>
				<td colspan="4"><input type="text" style="width:100%" name="data[name]" value="{$object.name|escape:'html'|escape:'quotes'}" /></td>
			</tr>
			<tr>				
				<th>{t}surname{/t}:</th>
				<td colspan="4"><input type="text" style="width:100%" name="data[surname]" value="{$object.surname|escape:'html'|escape:'quotes'}" /></td>
			</tr>	

			
			<tr id="gender" class="labelperson">
				<td></td>
				<td colspan="6">
					<input type="radio" name="data[gender]" id="gender-male" value="male" {if (!empty($object.gender) && $object.gender=='male')}checked="checked"{/if}/> <label for="gender-male">{t}male{/t}</label>
					<input type="radio" name="data[gender]" id="gender-female" value="female" {if (!empty($object.gender) && $object.gender=='female')}checked="checked"{/if}/> <label for="gender-female">{t}female{/t}</label>
					<input type="radio" name="data[gender]" id="gender-trans" value="transgender" {if (!empty($object.gender) && $object.gender=='transgender')}checked="checked"{/if}/> <label for="gender-trans">{t}transgender{/t}</label>
				</td>
			</tr>

			<tr>
				<th nowrap><span class="labelperson">{t}birthdate{/t}</span><span class="labelcompany">{t}active from{/t}</span>:</th>
				<td style="width:210px"><input type="text" class="dateinput" name="data[birthdate]" value="{if !empty($object.birthdate)}{$object.birthdate|date_format:$conf->datePattern}{/if}"/></td>
                {$hideFields = $conf->hideFields.addressbook|default:[]}
                {if !in_array('deathdate', $hideFields)}
                    <th style="width:140px; text-align: right;"><span class="labelperson">{t}deathdate{/t}:</span><span class="labelcompany">{t}to{/t}:</span></th>
                    <td><input type="text" class="dateinput" name="data[deathdate]" value="{if !empty($object.deathdate)}{$object.deathdate|date_format:$conf->datePattern}{/if}"/></td>
                {/if}
            </tr>
		</table>

</fieldset>


<div class="tab"><h2>{t}Address{/t}</h2></div>

<fieldset id="address">
<table>
	<tr>
		<th>{t}street name and #{/t}:</th>
		<td>
			<input style="width:240px;" type="text" name="data[street_address]" value="{$object.street_address|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
	<tr>
		<th>{t}city and zip code{/t}:</th>
		<td>
			<input type="text" name="data[city]" value="{$object.city|escape:'html'|escape:'quotes'}" />
			<input style="width:60px;" type="text" name="data[zipcode]" value="{$object.zipcode|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
	<tr>
		<th>{t}country{/t}:</th>
		<td>
			<select type="text" name="data[country]" id="country">
				{foreach $country_list_iso as $country}
				  <option value="{$country}" {if $object.country == $country}selected{/if}>{$country}</option>
				  {*if !in_array($val,$object_master_langs)}
				  <option value="{$val}" {if $val=="eng"}selected{/if}>{$label}</option>
				  {/if*}
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<th>{t}state{/t}:</th>
		<td><input type="text" name="data[state_name]" value="{$object.state_name|escape:'html'|escape:'quotes'}" /></td>
	</tr>
</table>

</fieldset>


<div class="tab"><h2>{t}Contacts{/t}</h2></div>

<fieldset id="contacts">
<table>
	<tr>
		<th>{t}email{/t}:</th>
		<td><input type="text" name="data[email]" value="{$object.email|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}email2{/t}:</th>
		<td><input type="text" name="data[email2]" value="{$object.email2|escape:'html'|escape:'quotes'}" /></td>
	</tr>
	<tr>
		<th>{t}phone{/t}:</th>
		<td><input type="text" name="data[phone]" value="{$object.phone|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}phone2{/t}:</th>
		<td><input type="text" name="data[phone2]" value="{$object.phone2|escape:'html'|escape:'quotes'}" /></td>
	</tr>
	<tr>
		<th>{t}fax{/t}:</th>
		<td><input type="text" name="data[fax]" value="{$object.fax|escape:'html'|escape:'quotes'}" /></td>
		<th>{t}website{/t}:</th>
		<td><input type="text" name="data[website]" value="{$object.website|escape:'html'|escape:'quotes'}" /></td>
	</tr>
</table>

</fieldset>


<div class="tab"><h2>{t}Description{/t}</h2></div>

<fieldset id="note">
	<textarea name="data[description]" class="mce description" style="font-size:13px; width:510px; height:150px;">{$object.description|default:''}</textarea>
</fieldset>