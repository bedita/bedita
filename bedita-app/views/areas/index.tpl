{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/plugins/jquery.form", false)}

{$html->script("libs/jquery/ui/jquery.ui.sortable.min", true)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$view->element('modulesmenu',['searchDestination' => 'results'])}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

<div class="head">

	<h1>{$object.title|default:''|escape}</h1>

</div> 

{if !empty($object)}

	{assign_concat var="actionForm" 1="save" 2=$objectType|capitalize|default:"Area"}
	
	<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">

	<div class="main">

		{assign_concat var="formDetails" 1="./inc/form_" 2=$objectType 3=".tpl"}
		{include file=$formDetails}

		{$beForm->csrf()}
		<div id="loading" style="position:absolute; left:320px; top:110px; ">&nbsp;</div>

	</div>
	
	</form>

	{$view->element('menuright')}

{/if}