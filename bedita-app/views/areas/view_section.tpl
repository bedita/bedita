{$html->script("jquery/jquery.selectboxes.pack", false)}

{assign_associative var="cssOptions" inline=false}
{$html->css('tree', null, $cssOptions)}

{$html->script("form", false)}
{$html->script("jquery/jquery.changealert", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.selectboxes.pack", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}


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
	<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	
	<div class="tab"><h2>{t}Properties{/t}</h2></div>
		
		{include file="inc/form_section.tpl"  method="viewSection"}
	
	</form>
</div>

{$view->element('menuright')}
