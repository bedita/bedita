<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">


<head>
	<title>B.Edita::{$title_for_layout}</title>          
	<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />

	<meta name="author" content="" >
	<meta http-equiv="content-type" content="text/html; charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<meta name="description" content="Descrizione" lang="it" >
	<meta name="keywords" content="Keys" >

	{$bevalidation->setup('it')}
	{$javascript->link("jquery")}
	{$javascript->link("jquery.cookie")}
	{$javascript->link("common")}
	{$html->css('cake.generic')}
	{$html->css('yav')}

{$content_for_layout}		
		
<div id="footerPage">
&nbsp;
	<a href="http://www.cakephp.org/" target="_new">
	{*assign_associative var=params alt="CakePHP : Rapid Development Framework", border="0"}
	{$html->image('cake.power.png', $parmas)*}
	{htmlHelper fnc="image" args="'cake.power.png', array('alt' => 'CakePHP : Rapid Development Framework', 'border' => '0')"}
	</a>
</div>

<?php echo $cakeDebug?>
</body>
</html>