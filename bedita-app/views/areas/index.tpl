{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/plugins/jquery.form", false)}

{$html->script("libs/jquery/ui/jquery.ui.sortable.min", true)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

<div class="head">

	<h1>{$object.title|default:''}</h1>

</div> 

{if !empty($object)}

	{assign_concat var="actionForm" 1="save" 2=$objectType|capitalize|default:"Area"}
	
	<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">

	<div class="main">

		{$relcount = $sections|@count|default:0}
		<div class="tab"><h2 {if $relcount == 0}class="empty"{/if}>{t}Sections{/t} {if $relcount > 0} &nbsp; <span class="relnumb">{$relcount}</span>{/if}</h2></div>
		<div id="areasectionsC">
			{include file='./inc/list_sections.tpl'}
		</div>

		{$relcount = $objects|@count|default:0}
		<div class="tab"><h2 {if $relcount == 0}class="empty"{/if}>{t}Contents{/t} {if $relcount > 0} &nbsp; <span class="relnumb">{$relcount}</span>{/if}</h2></div>
		<div id="areacontentC">
			{include file='./inc/list_content.tpl'}
		</div>

		{assign_concat var="formDetails" 1="./inc/form_" 2=$objectType 3=".tpl"}
		{include file=$formDetails}

	</div>

	</form>

	{$view->element('menuright')}

{/if}