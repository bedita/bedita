<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BEdita installation wizard | Finish</title>
	<style>
	{include file="../css/setup.css"}
	</style>
</head>
<body>

<h1>BEdita installation wizard</h1>

{assign var="page" value=$smarty.post.page|default:4}

{include file="inc/menu.tpl" page=$page}

<div style="float:left">

<form method="post" action='index.php'>
<fieldset>

<h3>Congratulations!</h3>
<p>BEdita is now installed on your system.</p>

{if !empty($usercreationok)}
<p>A new<span class="INFO">administrator user</span> has been created: {$userid}</p>
{/if}

{if !empty($endinstallfileerr)}
<p><span class="ERROR">[ERROR]</span> File <code>bedita.cfg.php</code> cannot be created. Check filesystem permissions</p>
{else}
<p>Now it's time to... <input style="float:right;" type="submit" value="Start with BEdita" onclick="javascript:document.getElementById('p').value = 5;" /></p>
{/if}

{*<input type="hidden" id="p" name="page" value="4"/>
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 3;" />*}

</fieldset>
</form>

</div>

</body>
</html>