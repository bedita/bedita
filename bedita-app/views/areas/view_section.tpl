{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$html->css('tree', null, ['inline' => false])}

{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}


<script type="text/javascript">
	$(document).ready( function ()
	{
		var openAtStart ="#properties";
		$(openAtStart).prev(".tab").BEtabstoggle();	
	});
</script>


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewSection"}

<div class="head">
<h1>
	{if (empty($object))}
	
		{t}Create new section{/t}
	
	{else}
	
		{$object.title|escape|default:"no title"}
	
	{/if}
</h1>	
</div> 

{include file="inc/menucommands.tpl" method="viewSection"}

<div class="main">
	<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	{$beForm->csrf()}
	{if (!empty($object))}

		<div class="tab"><h2>{t}Properties{/t}</h2></div>

	{/if}
		
	{include file="inc/form_section.tpl"  method="viewSection"}
	
	</form>
</div>

{$view->element('menuright')}
