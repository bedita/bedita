{$this->Html->script("jquery/jquery.selectboxes.pack", false)}

{assign_associative var="cssOptions" inline=false}
{$this->Html->css('tree', null, $cssOptions)}

{$this->Html->script("form", false)}
{$this->Html->script("jquery/jquery.changealert", false)}
{$this->Html->script("jquery/jquery.form", false)}
{$this->Html->script("jquery/jquery.selectboxes.pack", false)}
{$this->Html->script("jquery/jquery.cmxforms", false)}
{$this->Html->script("jquery/jquery.metadata", false)}


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

{include file="inc/menuleft.tpl" method="viewSection"}

<div class="head">
<h1>
	{if (empty($object))}
	
		{t}Create new section{/t}
	
	{else}
	
		{$object.title|default:"no title"}
	
	{/if}
</h1>	
</div> 

{include file="inc/menucommands.tpl" method="viewSection"}

<div class="main">
	<form action="{$this->Html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	
	<div class="tab"><h2>{t}Properties{/t}</h2></div>
		
		{include file="inc/form_section.tpl"  method="viewSection"}
	
	</form>
</div>

{$view->element('menuright')}
