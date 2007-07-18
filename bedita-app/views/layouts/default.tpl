{*

/**
 *
 * PHP versions 4 and 5
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 */
*}
{php}
$vs = &$this->get_template_vars() ;
{/php}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="it">
<head>
	<title>{$title_for_layout|default:'BEdita'}</title>          
	<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />

	<meta name="author" content="" >
	<meta http-equiv="content-type" content="text/html; charset=utf-8" >
	<meta http-equiv="Content-Style-Type" content="text/css" >
	<meta name="description" content="Descrizione" lang="it" >
	<meta name="keywords" content="Keys" >

	{$bevalidation->setup('it')}
	{$javascript->link("jquery")}
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