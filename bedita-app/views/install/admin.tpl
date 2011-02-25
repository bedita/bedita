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
<tr>
	<td><label>Admin user</label>*:</td>
	<td>{if !empty($usercreationok)}{$userid}{else}<input type="text" name="data[admin][user]" />{/if}</td>
	{if !empty($admin_user_empty)}<td><span class="ERROR">User cannot be empty</span></td>{/if}
</tr>
{if empty($usercreationok)}
<tr>
	<td><label>Password</label>*:</td>
	<td><input type="password" name="data[admin][password]"/></td>
	{if !empty($admin_pass_empty)}<td><span class="ERROR">Password cannot be empty</span></td>{/if}
</tr>
<tr>
	<td><label>Confirm password</label>*:</td>
	<td><input type="password" name="data[admin][cpassword]"/></td>
	{if !empty($cpassworderr)}<td><span class="ERROR">Password and Confirm password must match</span></td>{/if}
</tr>
{/if}
</table>

{if !empty($usercreationok)}
<p><span class="INFO">User created</span></p>
{/if}
{if !empty($usercreationerr)}
<p><span class="ERROR">Error saving user data</span></p>
{/if}

<h3>Web settings</h3>

<table>
<tr>
	<td><span class="{$bedita_url_check.severity}">[{$bedita_url_check.severity}]</span></td>
	<td><label>Bedita url</label>:</td>
	<td><code>{$bedita_url|default:'http://localhost/bedita'}</code></td>
	<td><span class="{$bedita_url_check.severity}">{$bedita_url_check.status}</span></td>
</tr>
<tr>
	<td><span class="{$media_root_check.severity}">[{$media_root_check.severity}]</span></td>
	<td><label>Media root</label>:</td>
	<td><code>{$media_root|default:'/var/www/bedita/webroot/files'}</code></td>
	<td><span class="{$media_root_check.severity}">{$media_root_check.status}</span></td>
</tr>
<tr>
	<td><span class="{$media_url_check.severity}">[{$media_url_check.severity}]</span></td>
	<td><label>Media url</label>:</td>
	<td><code>{$media_url|default:'http://localhost/bedita/files'}</code></td>
	<td><span class="{$media_url_check.severity}">{$media_url_check.status}</span></td>
</tr>
</table>

<h3>Mod rewrite</h3>

{if $mod_rewrite_php == $mod_rewrite_cakephp}
<p><span class="INFO">[INFO]</span>: <span>Mod Rewrite for PHP and CakePhp</span>: <span class="INFO">{$mod_rewrite_php}</span></p>
{else}
<p><span class="ERROR">[ERROR]</span>: <span>Mod Rewrite is</span> <span class="ERROR">{$mod_rewrite_php}</span> for PHP and <span class="ERROR">{$mod_rewrite_cakephp}</span> for CakePhp</p>
<p><span class="INFO">[INFO]</span>: The wizard will try to set CakePhp to {$mod_rewrite_php} [file <code>config/core.php</code> must be writable by php/webserver]</p>
{/if}

{if empty($usercreationok)}
<p><input type="submit" value="Apply changes" /></p>
{/if}

<input type="hidden" name="p_from" value="3"/>
<input type="hidden" id="p" name="page" value="3"/>
<input type="submit" value="< Back" onclick="javascript:document.getElementById('p').value = 2;" />
{if empty($usercreationok)}
<input type="button" value="Next >" disabled="disabled"  />
{else}
<input type="submit" value="Next >" onclick="javascript:document.getElementById('p').value = 4;" />
{/if}

</fieldset>
</form>

</div>

</body>
</html>