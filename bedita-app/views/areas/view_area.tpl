{$html->css('tree', null, null, false)}
{$javascript->link("jquery/jquery.treeview", false)}

{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}

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
	
		{$object.title|default:"no title"}
	
	{/if}
</h1>	
</div> 

{include file="inc/menucommands.tpl" fixed=true}


<div class="main">
	<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	
	{include file="inc/form_area.tpl"}
	
</div>

{$view->element('menuright')}


