<form action="{$html->url('/home/editProfile')}" method="post">
<table class="condensed">
<tr>
	<td><label class="simple" id="lrealname" for="realname">{t}name{/t}</label></td>	<td>
	<input type="hidden" name="data[User][id]" value="{$BEAuthUser.id}"/>
	<input type="hidden" name="data[User][userid]" value="{$BEAuthUser.userid}"/>
	<input type="hidden" name="data[User][valid]" value="{$BEAuthUser.valid}"/>
	<input type="text" id="realname"  name="data[User][realname]" value="{$BEAuthUser.realname}"  />
	</td>
</tr>
<tr>
	<td><label class="simple" id="lemail" for="email">{t}email{/t}</label></td>	<td><input type="text" id="email" name="data[User][email]" value="{$BEAuthUser.email}" class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/></td>
</tr>


<tr>
	<td><label>{t}language{/t}</label></td>
	<td>
{if !empty($conf->multilang) && $conf->multilang}
	<select name="data[User][lang]">
		<option value="">--</option>
	{foreach key=key item=item name=l from=$conf->langsSystem}
		<option value="{$key}" {if $key == $BEAuthUser.lang}selected{/if}>{$item}</option>
	{/foreach}
	</select>
{/if}
	</td>
</tr>

<tr><td colspan=2><hr /></td></tr>

<tr>
	<td><label class="simple">{t}old psw{/t}</label></td>	<td><input type="password" name="oldpwd" value="" id="oldpwd" class="{if isset($userdetail)}{literal}{password:true}{/literal}{else}{literal}{required:true,password:true}{/literal}{/if}" title="{$tr->t($conf->passwdRegexMsg)}"/></td>
</tr>
<tr>
	<td><label class="simple">{t}new psw{/t}</label></td>	<td><input type="password" name="pwd" value="" id="pwd" class="{if isset($userdetail)}{literal}{password:true}{/literal}{else}{literal}{required:true,password:true}{/literal}{/if}" </td>
</tr>
<tr>
	<td><label class="simple">{t}new again{/t}</label></td>	<td><input type="password" name="data[User][passwd]" value="" class="{literal}{equalTo:'#pwd'}{/literal}" title="{t}Passwords should be equal{/t}"/></td>
</tr>

<tr><td colspan=2><hr /></td></tr>

<tr>
	<td><label>{t}notify me by email{/t}</label></td>
	<td>
		<select name="data[User][comments]"> 
			<option value="never"{if $BEAuthUser.comments == "never"} selected{/if}>{t}never{/t}</option>
			<option value="mine"{if $BEAuthUser.comments == "mine"} selected{/if}>{t}mine{/t}</option>
			<option value="all"{if $BEAuthUser.comments == "all"} selected{/if}>{t}all{/t}</option>
		</select> {t}new comments{/t}
		<br />
		<select name="data[User][notes]"> 
			<option value="never"{if $BEAuthUser.notes == "never"} selected{/if}>{t}never{/t}</option>
			<option value="mine"{if $BEAuthUser.notes == "mine"} selected{/if}>{t}mine{/t}</option>
			<option value="all"{if $BEAuthUser.notes == "all"} selected{/if}>{t}all{/t}</option>
		</select> {t}new notes{/t}
		<br />
		<input type="checkbox" name="data[User][notify_changes]" value="1"{if $BEAuthUser.notify_changes == 1} checked{/if}> {t}changes on my contents{/t}
		{*<br />
		<input type="checkbox"> {t}reports{/t}
		*}
	</td>
</tr>
</table>
<hr />

<input type="submit" value="{t}save profile{/t}" />
</form>
