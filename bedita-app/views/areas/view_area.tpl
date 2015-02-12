{$html->css('tree', null, ['inline' => false])}

{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}

{assign var='object' value=$area}

<script type="text/javascript">
	{literal}
	$(document).ready( function ()
	{
		var openAtStart ="#properties";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
	{/literal}
</script>


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
<h1>
	{if (empty($object))}
	
		{t}Create new publication{/t}
	
	{else}
	
		{$object.title|escape|default:"no title"}
	
	{/if}
</h1>	
</div> 

{include file="inc/menucommands.tpl" fixed=true}


<div class="main">
	<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	{$beForm->csrf()}
	{include file="inc/form_area.tpl"}
	
</div>

{$view->element('menuright')}


