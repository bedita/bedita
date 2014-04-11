{$html->script('form', false)}
{$html->script('libs/jquery/plugins/jquery.form', false)}
{$html->script('libs/jquery/plugins/jquery.changealert', false)}

{$view->element('modulesmenu')}

{include file='inc/menuleft.tpl'}

{include file='inc/menucommands.tpl'}

{$view->element('toolbar')}

<div class="mainfull">

	{$view->element('filters', [
		'options' => ['mediaTypes' => true, 'categories' => false]
	])}
	
	{include file='./inc/list_streams.tpl' streamTitle='multimedia'}
	
	{bedev}
	{*
	{include file='./inc/list_streams_table.tpl' streamTitle='multimedia'}
	 *}
	{/bedev}
	
</div>

