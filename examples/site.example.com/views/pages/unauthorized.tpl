{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | Error 403 - Unauthorized</title>
	{$html->css('beditaBase')}

</head>
<body>

<div class="top">

	<div class="logo">
		<a title="{$publication.public_name}" href="{$html->url('/')}"><img src="{$html->webroot}img/BElogo24.png" alt="" /></a>
	</div>

	<div class="moduli" style="font-size:0.8em">
		<h1>Error 403</h1>
		You are not authorized to access this item
	</div>
		
</div>

{if $conf->debug >= 1} 
<pre>
ErrorType:	{$errorType|default:''}
Details: 	{$details|default:''}
Result: 	{$result|default:''}

Action: 	{$action|default:''}
Controller: {$controller|default:''}
File: 		{$file|default:''}
Title: 		{$title|default:''}

</pre>
{/if}

{$view->element('footer')}

</body>
