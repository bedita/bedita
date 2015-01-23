{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
<div class="primacolonna">


	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>

	{$view->element('user_module_perms')}

</div>