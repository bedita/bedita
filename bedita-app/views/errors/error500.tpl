{*
** Error 500 - Smarty template
*}
{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | Error 500 - Internal Server Error</title>
	{$html->css('beditaNew')}
	{$html->script("jquery/jquery")}
	{$html->script("beditaUI")}
</head>
<body>

<div class="primacolonna">
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
</div>

<div class="secondacolonna">
	<div id="messagesDiv" style="display:block">
		<div class="message error" style="border-top:0px; border-left:1px solid silver">
			<h2>Error 500</h2>
			<br />
			<p>Internal Server Error</p>
		</div>
	</div>
</div>
</html>

</body>