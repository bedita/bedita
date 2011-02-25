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

<h3>Compliments!</h3>
<p>You installed BEdita correctly.</p>

{if !empty($endinstallfileerr)}
<p><span class="ERROR">[ERROR]</span> File <code>install.done</code> cannot be created. Check filesystem permits</p>
{else}
<p>Now it's time to... <input type="submit" value="Start with BEdita" onclick="javascript:document.getElementById('p').value = 5;" /></p>
{/if}

<input type="hidden" id="p" name="page" value="4"/>
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 3;" />
<input type="button" value="Next >" disabled="disabled" />

</fieldset>
</form>

</div>

</body>
</html>