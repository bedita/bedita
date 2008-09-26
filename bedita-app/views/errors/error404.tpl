{*
** Error 404 - Smarty template
** Replacement for default CakePHP error404.ctp
** 
*}
{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | Error 404 - Missing page</title>
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
			<h2>Error 404</h2>
			<br />
			<p>Missing Page</p>
		</div>
	</div>
</div>




{* Da finire
<p class="error">
	<strong><?php __('Error'); ?>: </strong>
	<?php echo $message; ?>
</p>
*}

</body>