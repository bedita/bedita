<form action="{$html->url('/home/editProfile')}" method="post">
<table class="condensed">
<tr>
	<td><label class="simple" id="lrealname" for="realname">{t}name{/t}</label></td>
	<td>
	<input type="hidden" name="data[User][id]" value="{$BEAuthUser.id}"/>
	<input type="hidden" name="data[User][userid]" value="{$BEAuthUser.userid}"/>
	<input type="hidden" name="data[User][valid]" value="{$BEAuthUser.valid}"/>
	<input type="text" id="realname"  name="data[User][realname]" value="{$BEAuthUser.realname}"  />
	</td>
</tr>
<tr>
	<td><label class="simple" id="lemail" for="email">{t}email{/t}</label></td>
	<td><input type="text" id="email" name="data[User][email]" value="{$BEAuthUser.email}" class="{ email:true}" title="{t}Use a valid email{/t}"/></td>
</tr>

<tr>
	<td><label>{t}language{/t}</label></td>
	<td>
	<select name="data[User][lang]">
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
	<td><input type="password" name="oldpwd" value="" id="oldpwd" class="{if isset($userdetail)}{ password:true}{else}{ required:true,password:true}{/if}"/></td>
</tr>
<tr>
	<td><label class="simple">{t}new psw{/t}</label></td>
	<td><input type="password" name="pwd" value="" id="pwd" class="{if isset($userdetail)}{ password:true}{else}{ required:true,password:true}{/if}"></td>
</tr>
<tr>
	<td><label class="simple">{t}new again{/t}</label></td>
	<td><input type="password" name="data[User][passwd]" value="" class="{ equalTo:'#pwd'}" title="{t}Passwords should be equal{/t}"/></td>
</tr>

<tr><td colspan=2><hr /></td></tr>

</table>

<script type="text/javascript">
$(document).ready(function(){
$(".checko").change(function(){
	var target = $(this).attr('rel');
	if ($(this).is(':checked'))	{
	  	$('#'+target).show().val(['all']);
	} else {
		$('#'+target).hide().val(['never']);
	}
});
});
</script>

<table class="condensed">
<tr>
	<td colspan=2><label>{t}notify me by email{/t}</label></td>
</tr>
<tr>
	<td>
		<input class="checko" name="comments" value="1" rel="usercomments" type="checkbox" {if !empty($BEAuthUser.comments) && ($BEAuthUser.comments != "never")} checked{/if}>
		{t}new comments{/t}
	</td>
	<td>
		<select id="usercomments" name="data[User][comments]" {if empty($BEAuthUser.comments) or ($BEAuthUser.comments == "never")}style="display:none"{/if}>
			<option value="mine"{if $BEAuthUser.comments == "mine"} selected{/if}>{t}on my stuff only{/t}</option>
			<option value="all"{if $BEAuthUser.comments == "all"} selected{/if}>{t}all{/t}</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<input class="checko" name="notes" value="1" rel="usernotes" type="checkbox" {if !empty($BEAuthUser.notes) && ($BEAuthUser.notes != "never")} checked{/if}>
		{t}new notes{/t}</td>
	<td>
		<select id="usernotes" name="data[User][notes]" {if empty($BEAuthUser.notes) or ($BEAuthUser.notes == "never")}style="display:none"{/if}> 
			<option value="mine"{if $BEAuthUser.notes == "mine"} selected{/if}>{t}on my stuff only{/t}</option>
			<option value="all"{if $BEAuthUser.notes == "all"} selected{/if}>{t}all{/t}</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan=2>
		<input type="checkbox" name="data[User][notify_changes]" value="1"{if $BEAuthUser.notify_changes == 1} checked{/if}>
		{t}changes on my contents{/t}
	</td>
</tr>
</table>
<hr />

<input type="submit" value="{t}save profile{/t}" />
</form>
