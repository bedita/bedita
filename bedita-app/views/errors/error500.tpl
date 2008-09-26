{*
** Error 500 - Smarty template
*}
{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | Error 500 - Internal Server Error</title>
	{$html->css('beditaNew')}
	{$javascript->link("jquery/jquery")}
	{$javascript->link("beditaUI")}
</head>
<body>

<div class="primacolonna">
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
</div>

<div class="secondacolonna">
	<div id="messagesDiv" style="margin-top:10px;display:block">
		<div class="message error">
			<h2>Error 500</h2>
			<br />
			<p>Internal Server Error</p>
		</div>
	</div>
</div>

</body>