{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title_for_layout|default:'BEdita'}</title>     
<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
{$bevalidation->setup('it')}
{$javascript->link("common")}
{$javascript->link("jquery")}
{$html->css('cake.generic')}
{$html->css('yav')}
{$content_for_layout}		
<div id="footer">
&nbsp;
<a href="http://www.cakephp.org/" target="_blank">
{htmlHelper fnc="image" args="'cake.power.png', array('alt' => 'CakePHP : Rapid Development Framework', 'border' => '0')"}
</a>
</div>
</body>
</html>