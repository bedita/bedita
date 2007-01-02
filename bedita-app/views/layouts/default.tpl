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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title_for_layout|default:'BEdita'}</title>          
<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />

{$bevalidation->setup('it')}
{$html->css('cake.generic')}

{$content_for_layout}		
		
		
<div id="footer">
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