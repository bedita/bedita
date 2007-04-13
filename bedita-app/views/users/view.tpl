<script type="text/javascript">
{literal}
<!--

function cmpPasswd() {
	var p1 = document.getElementById('UserPassw').value;
	var p2 = document.getElementById('UserPassw2').value;
	if (p1 == p2)
		return null;
	return "Le password non coincidono";
}

var rules = new Array();
rules[0]='UserUsername:userid|required';
rules[1]='UserEmail:Email|required';
rules[2]='UserEmail:Email|email';
rules[3]='cmpPasswd()|custom';
-->
{/literal}
</script>

</head>

<body>

<h1>My Users</h1>
<div id="errorsDiv"></div>

<table border="0" cellspacing="0" cellpadding="0" class="mainTable">
	<tr>
		<td>
		<pre>LOGGED AND Allow</pre>
		{$html->link('Esci', '/Users/logout')}
		</td>
		<td>

{formHelper fnc="create" args="'login', array('action' => '/users/edit', 'type' => 'POST', 'id' => 'frmModifyUser', 'name' => 'frmModifyUser')"}

{assign var="back" 		value=$beurl->here()}
{assign var="id" 		value=$User.User.id}
{assign var="status"	value=$User.User.status}

{htmlHelper fnc="hidden" args="'back/OK', array('value' => '$back')"}
{htmlHelper fnc="hidden" args="'back/ERROR', array('value' => '$back')"}
{htmlHelper fnc="hidden" args="'User/id', array('value' => '$id')"}
{htmlHelper fnc="hidden" args="'User/status', array('value' => '$status')"}

<fieldset>
    <legend>&nbsp;{$User.User.nome} {$User.User.cognome}:&nbsp;</legend>

<table>
	<tr>
		<td>Userid:&nbsp;</td>
		<td>
		{assign var="value" value=$User.User.username}
		{htmlHelper fnc="input" args="'User/username', array('style' => 'width: 250px', 'value' => '$value')"}
		</td>
		
		<td rowspan="5" valign="top">
			{html_checkboxes name="data[Module][Module]" options=$moduleList selected=$User.Module}
		</td>
	</tr>
	<tr>
		<td>Nome:</td>
		<td>
		{assign var="value" value=$User.User.nome}
		{htmlHelper fnc="input" args="'User/nome', array('style' => 'width: 250px', 'value' => '$value')"}
		</td>
	</tr>
	<tr>
		<td>Cognome:</td>
		<td>
		{assign var="value" value=$User.User.cognome}
		{htmlHelper fnc="input" args="'User/cognome', array('style' => 'width: 250px', 'value' => '$value')"}
		</td>
	</tr>
	<tr>
		<td style="white-space:nowrap">Nuova Password:</td>
		<td>
		{assign var="value" value=$User.User.passw}
		{htmlHelper fnc="hidden" args="'User/crypted', array('style' => 'width: 250px', 'value' => '$value')"}
		{htmlHelper fnc="password" args="'User/passw', array('style' => 'width: 250px')"}
		</td>
	</tr>
	<tr>
		<td>Ripeti Password:</td>
		<td>
		{assign var="value" value=$User.User.passw}
		{htmlHelper fnc="password" args="'User/passw2', array('style' => 'width: 250px')"}
		<br/>(lascia vuoto per non modificarla)
		</td>
	</tr>
	<tr>
		<td>Email:</td>
		<td>
		{assign var="value" value=$User.User.email}
		{htmlHelper fnc="input" args="'User/email', array('style' => 'width: 250px', 'value' => '$value')"}
		</td>
	</tr>

	<tr>
		<td colspan="3">
        <label for="submit">&nbsp;</label>
		{htmlHelper fnc="submit" args="'Modifica', array('onclick' => 'if(!checkOnSubmit(\'frmModifyUser\',rules)) return false;')"}
		</td>
	</tr>
</table>    
    
    
</fieldset>

</form>

		</td>
	</tr>
</table>

{*

<table border="0" cellspacing="0" cellpadding="2" class="indexList">
<tr>
	<td class="gest_menu" style="width:535px; text-align:left; border:0px; border-left:1px solid white;" colspan="12">
		<h2>modifica utente</h2>
	</th>
</tr>
<tr>
	<th>username</th>
	<th>passw</th>
	<th>status</th>
<th>accesso ai moduli:</th>
</tr>


<input type="hidden" name="IDuser" value="{$users.id}">
<tr>
	<td><input type="text" name="username" value="{$users.username}"></td>
	<td><input type="text" name="passw" value="{$users.passw}"></td>
	<td>
		<input type="radio" name="status" value="on" {if $users.status=="on"}checked{/if}>on
		<input type="radio" name="status" value="off" {if $users.status != "on"}checked{/if}>off
	</td>
	<td rowspan="4">
	{html_checkboxes name="moduli" values=$optModuli.values selected=$users.moduli output=$optModuli.labels separator="<br />"}
	</td>
</tr>
<tr>
	<th>email</th>
	<th>nome</th>
	<th>cognome</th>
</tr>
<tr>
	<td><input type="text" name="email" value="{$users.email}"></td>
	<td><input type="text" name="nome" value="{$users.nome}"></td>
	<td><input type="text" name="cognome" value="{$users.cognome}"></td>
</tr>
<tr>
	<td colspan="3" style="height:220px;" align="center">
	<input type="submit" name="modifica" value="   salva   ">
	&nbsp;&nbsp;
	<input type="submit" name="elimina" onClick="return confirmSubmit('Vuoi veramente eliminare l\'utente?')" value="elimina">

	</td>
</tr>
</form>

</table>

</td></tr></table>

{include file="footer.tpl" home=1}

</body>
</html>
 *}