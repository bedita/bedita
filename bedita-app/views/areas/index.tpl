{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/plugins/jquery.form", false)}

{$html->script("libs/jquery/ui/jquery.ui.sortable.min", true)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

<div class="head">

	<h1>{$object.title|default:''|escape}</h1>

</div> 

{if !empty($object)}

	{assign_concat var="actionForm" 1="save" 2=$objectType|capitalize|default:"Area"}
	
	<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">
	{$beForm->csrf()}
	<div id="loading" style="position:absolute; left:320px; top:110px; ">&nbsp;</div>

	<div class="main full">

	<!--
		<div class="tab"><h2>{t}Details{/t} of &nbsp; <span class="graced" style="font-size:1.5em" id="sectionTitle"></span></h2></div>
	-->

		<fieldset style="padding:0px" id="properties">		

			{*
			<!-- a causa di IE non si pole usare questo -->
			<ul class="htab">
				<li rel="areacontentC">{t}contents{/t}</li>
				<li rel="areasectionsC">{t}sections{/t}</li>
				<li rel="areapropertiesC">{t}properties{/t}</li>
			</ul>
			<!-- per IE -->			
			*}

			<!-- questo è brutto ma cross-browser -->
			<table class="htab">
				<td rel="areacontentC">{t}all contents{/t}</td>
				<td rel="areasectionsC">{t}sections only{/t}</td>
				<td rel="areapropertiesC">{t}properties{/t}</td>
			</table>	
			<!-- -->	

			<div class="htabcontainer" id="sectiondetails">

				<div id="areacontentC" class="htabcontent">

					{include file='./inc/list_content.tpl'}

				</div>

				<div id="areasectionsC" class="htabcontent">

					{include file='./inc/list_sections.tpl'}

				</div>


				<div id="areapropertiesC" class="htabcontent">
					
					{assign_concat var="formDetails" 1="./inc/form_" 2=$objectType 3=".tpl"}
					{include file=$formDetails}

				</div>

			</div>

		</fieldset>	

	</div>

	</form>

	{$view->element('menuright')}

{/if}