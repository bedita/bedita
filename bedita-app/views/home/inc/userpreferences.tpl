<fieldset id="bioprofile">
	<table class="condensed" style="width: 100%">
		
		<tr>
			<td><label class="simple">userid</label></td>
			<td><input type="text" style="width:100%" readonly=1 value="{$BEAuthUser.userid|escape}" /></td>
		</tr>	

		<tr>
			<td><label class="simple" id="lrealname" for="realname">{t}name{/t}</label></td>
			<td>
			<input type="hidden" name="data[User][id]" value="{$BEAuthUser.id}"/>
			<input type="hidden" name="data[User][userid]" value="{$BEAuthUser.userid|escape}"/>
			<input type="hidden" name="data[User][valid]" value="{$BEAuthUser.valid}"/>
			<input type="text" id="realname" style="width:100%" name="data[User][realname]" value="{$BEAuthUser.realname|escape}"  />
			</td>
		</tr>
		<tr>
			<td><label class="simple" id="lemail" for="email">{t}email{/t}</label></td>
			<td><input type="text" id="email" style="width:100%" name="data[User][email]" value="{$BEAuthUser.email}" class="{ email:true}" title="{t}Use a valid email{/t}"/></td>
		</tr>

		<tr>
			<td><label>{t}language{/t}</label></td>
			<td>
			<select name="data[User][lang]" style="width:100%">
				<option value="">--</option>
			{foreach key=key item=item name=l from=$conf->langsSystem}
				<option value="{$key}" {if $key == $BEAuthUser.lang}selected{/if}>{$item}</option>
			{/foreach}
			</select>
			</td>
		</tr>

		<tr><td colspan=2><hr /></td></tr>

		<tr>
			<td><label class="simple">{t}old psw{/t}</label></td>
			<td><input type="password" name="oldpwd" value="" id="oldpwd" class="{if isset($userdetail)}{ password:true}{else}{ required:true,password:true}{/if}" autocomplete="off"/></td>
		</tr>
		<tr>
			<td><label class="simple">{t}new psw{/t}</label></td>
			<td><input type="password" name="pwd" value="" id="pwd" class="{if isset($userdetail)}{ password:true}{else}{ required:true,password:true}{/if}" autocomplete="off"></td>
		</tr>
		<tr>
			<td><label class="simple">{t}new again{/t}</label></td>
			<td><input type="password" name="data[User][passwd]" value="" class="{ equalTo:'#pwd'}" title="{t}Passwords should be equal{/t}"/></td>
		</tr>

		<tr><td colspan=2><hr /></td></tr>

	</table>
</fieldset>