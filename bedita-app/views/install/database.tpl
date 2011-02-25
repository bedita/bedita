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

{assign var="page" value=$smarty.post.page|default:2}

{include file="inc/menu.tpl" page=$page}

<div style="float:left">
<h2>Database</h2>

<form method="post" action='index.php'>
<fieldset>

{if ($is_connected == "y")}
<p><span class="INFO">[INFO]</span>: <span>Database connection</span>: <span class="INFO">Cake is able to connect to database</span></p>
{if empty($database_sources)}
<p><input type="hidden" name="action" value="initdb" /><input type="submit" value="Init Database Schema" /></p>
{/if}
<input type="hidden" id="p" name="page" value="2" />
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 1;" />
{if empty($database_sources)}
<input type="submit" value="Next >" disabled="disabled" />
{else}
<input type="submit" value="Next >" onclick="javascript:document.getElementById('p').value = 3;" />
{/if}
{else}
{if !empty($dbconfigupdated)}
<p><span class="INFO">[INFO]</span>: <span>Database config</span>: <span class="INFO">Database configuration file updated.</span></p>
{/if}
{if !empty($cpassworderr)}
<p><span class="ERROR">[ERROR]</span>: <span>Password</span>: <span class="ERROR">Confirm Password does not match Password. Retry!</span></p>
{/if}
<p><span class="ERROR">[ERROR]</span>: <span>Database connection</span>: <span class="ERROR">Cake is NOT able to connect to database</span></p>
<p>Create a new database with specific user and password, if you haven&#39;t done already, and fill in Database settings properly.</p>
<p><span class="{if ($dbfile_writable == "n")}ERROR{else}INFO{/if}">[{if ($dbfile_writable == "n")}ERROR{else}INFO{/if}]</span>: File <code>{$dbfile}</code> {if ($dbfile_writable == "n")}<span class="ERROR">IS NOT</span>{else}<span class="INFO">IS</span> {/if} writable</p>
{if ($dbfile_writable == "n")}
<p>If you want to apply changes, file 'database.php' must be 'writable' by webserver user.</p>
<p>On linux, open a terminal and: <code>$ sudo chown www-data:www-data {$dbfile}</code></p>
{/if}
<h3>Database Settings</h3>
<table>
<tr><td><label>Database</label>:</td><td><input type="text" name="data[database][database]" value="{$database_config.database|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Login</label>:</td><td><input type="text" name="data[database][login]" value="{$database_config.login|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Password</label>:</td><td><input type="password" name="data[database][password]" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Confirm Password</label>:</td><td><input type="password" name="data[database][cpassword]" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
</table>
<p>Advanced settings</p>
<table>
<tr><td><label>Host</label>:</td><td><input type="text" name="data[database][host]" value="{$database_config.host|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Port</label>:</td><td><input type="text" name="data[database][port]" value="{$database_config.port|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Driver</label>:</td><td><input type="text" name="data[database][driver]" value="{$database_config.driver|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Persistent</label>:</td><td><input type="text" name="data[database][persistent]" value="{$database_config.persistent|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Schema</label>:</td><td><input type="text" name="data[database][schema]" value="{$database_config.schema|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Prefix</label>:</td><td><input type="text" name="data[database][prefix]" value="{$database_config.prefix|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Encoding</label>:</td><td><input type="text" name="data[database][encoding]" value="{$database_config.encoding|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
<tr><td><label>Connect</label>:</td><td><input type="text" name="data[database][connect]" value="{$database_config.connect|default:''}" {if ($dbfile_writable == "n")}readonly="readonly"{/if}/></td></tr>
</table>

<input type="hidden" id="p" name="page" />
<input type="hidden" name="dbconfig_modify" value="{$dbfile_writable}"/>
<input type="submit" value="Save and Check again" onclick="javascript:document.getElementById('p').value=2;" />
<hr/>
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 1;" />
<input type="button" value="Next >" disabled="disabled" />
{/if}

</fieldset>
</form>

</div>
</body>
</html>