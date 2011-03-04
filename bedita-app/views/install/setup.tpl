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

{assign var="page" value=$smarty.post.page|default:1}

{include file="inc/menu.tpl" page=$page}

<div style="float:left">
<h2>Smarty Template Engine</h2>
{foreach from=$results_smarty item=i}
<p><span class="{$i.severity}">[{$i.severity}]</span>: <code>{$i.label}</code>: <span class="{$i.severity}">{$i.description}</span></p>
{/foreach}

<h2>CakePHP</h2>
{foreach from=$results_cake item=i}
<p><span class="{$i.severity}">[{$i.severity}]</span>: <code>{$i.label}</code>: <span class="{$i.severity}">{$i.description}</span></p>
{/foreach}

{if !empty($results_install)}
<h2>Installation</h2>
{foreach from=$results_install item=i}
<p><span class="{$i.severity}">[{$i.severity}]</span>: <code>{$i.label}</code>: <span class="{$i.severity}">{$i.description}</span></p>
{/foreach}
{/if}

<hr/>
{if $n_errors > 0}
<p>{$n_errors} error(s) to resolve before continue</p>
{else}
<p>No error(s) found: you can continue</p>
{/if}
<hr/>

<form method="post" action='index.php'>
<fieldset>
	<input type="hidden" id="p" name="page" />
	<input type="submit" value="Perform Check" onclick="javascript:document.getElementById('p').value=1;"/>
<hr/>
	<input type="button" value="< Back" disabled="disabled" />
{if $n_errors > 0}
	<input type="submit" value="Next >" disabled="disabled" />
{else}
	<input type="submit" value="Next >" onclick="javascript:document.getElementById('p').value=2;" />
{/if}
</fieldset>
</form>

</div>
</body>
</html>