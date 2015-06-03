<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BEdita 3.5 setup | Admin</title>
	<style>
	{include file="../css/setup.css"}
	</style>
</head>
<body>

<h1>BEdita 3.5 setup</h1>

{assign var="page" value=$smarty.post.page|default:3}

{include file="inc/menu.tpl" page=$page}

<div style="float:left">

<form method="post" action='index.php'>
<fieldset>

<h3>Administrator</h3>
<p>
	{* #540 - Prevent overwriting of system user. *}
	<span class="INFO">[INFO]</span>: a system user <code>{$existingUser}</code>{if $defaultPassword} with password <code>bedita</code>{/if} has already been set up.
	You can choose to <b>add</b> another administrator or <b>replace</b> the system user.
</p>
<p>
	{* #618 - Add informations about system user to setup. *}
	Please note that the system user (user with <code>ID = 1</code>) <b>will not be deletable</b>, and will be also used as default author for contents created or edited by system tasks.
</p>
<p>Please insert username and password for the administrator user.</p>

<table>
<tr>
	<td><label>Admin user</label>*:</td>
	<td><input tabindex="1" type="text" name="data[admin][user]" /></td>
	{if !empty($admin_user_empty)}<td><span class="ERROR">User cannot be empty</span></td>{/if}
</tr>
{if empty($usercreationok)}
<tr>
	<td><label>Password</label>*:</td>
	<td><input tabindex="2" type="password" name="data[admin][password]"/></td>
	{if !empty($admin_pass_empty)}<td><span class="ERROR">Password cannot be empty</span></td>{/if}
</tr>
<tr>
	<td><label>Confirm password</label>*:</td>
	<td><input tabindex="3" type="password" name="data[admin][cpassword]"/></td>
	{if !empty($cpassworderr)}<td><span class="ERROR">Password and Confirm password must match</span></td>{/if}
</tr>
<tr>
	<td><label>Overwrite system user</label>:</td>
	<td><input tabindex="4" type="checkbox" name="data[admin][_overwrite]" /></td>
</tr>
{/if}
</table>

{if !empty($usercreationerr)}
<p><span class="ERROR">Error saving user data</span></p>
{/if}

{* all this settings are calculated on next step

<h3>Web settings (you can always change the urls later in BEdita admin->configuration)</h3>

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

*}


{if $mod_rewrite_php !== $mod_rewrite_cakephp}
<h3>Mod rewrite</h3>
<p><span class="ERROR">[ERROR]</span>: <span>Mod Rewrite is</span> <span class="ERROR">{if $mod_rewrite_php == "askuser"}?{else}{$mod_rewrite_php}{/if}</span> for PHP and <span class="ERROR">{$mod_rewrite_cakephp}</span> for CakePhp</p>
	{if $mod_rewrite_php == "askuser"}
	<p><span class="WARN">[WARN]</span>: <span>Not able to say if mod_rewrite is enabled.</span></p>
	<p><span>Please check your webserver configuration and set properly the following preference.</span></p>
	<p><label>mod_rewrite</label> is: <input type="radio" value="enabled" name="mod_rewrite_enabled" />enabled <input type="radio" value="disabled" name="mod_rewrite_enabled" />disabled </p>
	{else}
	<p><span class="INFO">[INFO]</span>: The wizard will try to set CakePHP 'mod revrite' to {$mod_rewrite_php} [file <code>config/core.php</code> must be writable by php/webserver]</p>
	<p><span>Read the article <a href="http://docs.bedita.com/setup/handling-mod_rewrite-in-bedita-and-cakephp" target="_blank">Handling mod_rewrite in BEdita and CakePhp</a> 
		on <a href="http://docs.bedita.com" target="_blank">docs.bedita.com</a> for more information on this topic</span></p>
	{/if}
{/if}


<input type="hidden" name="p_from" value="2"/>
<input type="hidden" id="p" name="page" value="3"/>
<div id="buttons">
    <input tabindex="5" type="submit" style="float:right;" value="Next >" onclick="javascript:document.getElementById('p').value = 3;" />
    <input tabindex="6" type="submit" style="float:right;" value="< Back" onclick="javascript:document.getElementById('p').value = 2;" />
    <div style="clear:both"></div>
</div>

</fieldset>
</form>

</div>

</body>
</html>