<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BEdita 3.7 setup | Finish</title>
	<style>
	{include file="../css/setup.css"}
	</style>
</head>
<body>

<h1>BEdita 3.7 setup</h1>

{assign var="page" value=4}

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
    {if !empty($configWriteFail)}
        <p><span class="WARN">[WARNING]</span> Error writing configuration in <code>bedita.cfg.php</code>. You can continue and edit <code>$config['beditaUrl']</code> manually.
    {/if}
    <p>Now it's time to... <input style="float:right;" type="submit" value="Start with BEdita" onclick="javascript:document.getElementById('p').value = 5;" /></p>
{/if}

{*<input type="hidden" id="p" name="page" value="4"/>
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 3;" />*}

</fieldset>
</form>

</div>

</body>
</html>