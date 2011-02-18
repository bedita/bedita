<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita installation wizard</title>
	<style>
{literal}
		ul { list-style: none; }
		.INFO { color:green}
		.WARN { color:yellow }
		.ERROR { color:red }
		li.done { color: green; }
		li.curr { color: blue; }
		li.todo { color: orange; }
		div { margin:10px }
{/literal}
	</style>
</head>
<body>

<h1>BEdita installation wizard</h1>

{assign var="page" value=$smarty.post.page|default:3}

{include file="inc/menu.tpl" page=$page}

<div style="float:left">

<form method="post" action='index.php'>
<fieldset>

<h3>Administrator</h3>
<table>
<tr><td><label>Admin user</label>:</td><td><input type="text"/></td></tr>
<tr><td><label>Password</label>:</td><td><input type="password"/></td></tr>
<tr><td><label>Confirm password</label>:</td><td><input type="password"/></td></tr>
</table>

<h3>Web settings</h3>

<table>
<tr><td><label>Media root</label>:</td><td><input type="text" size="50" value="/var/www/bedita/webroot/files" /></td></tr>
<tr><td><label>Media url</label>:</td><td><input type="text" size="50" value="http://localhost/bedita/files"/></td></tr>
<tr><td><label>Mod Rewrite</label>:</td><td><input type="radio" />yes <input type="radio" />no </td></tr>
</table>

<input type="hidden" id="p" name="page" />
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 2;" />
<input type="button" value="Next >" disabled="disabled"  />
<input type="button" value="Finish" disabled="disabled" />

</fieldset>
</form>

</div>

</body>
</html>