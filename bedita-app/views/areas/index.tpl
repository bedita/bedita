{assign_associative var="cssOptions" inline=false}
{$html->css("ui.datepicker", null, $cssOptions)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.autogrow", false)}

{$html->script("jquery/ui/jquery.ui.sortable", true)}
{$html->script("jquery/jquery.selectboxes.pack", false)}

{$html->script("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
{$html->script("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}


<script type="text/javascript">
<!--
/* define urls for ajax calls. Used in webroot/js/module.areas.js */
ajaxContentsUrl 		= "{$html->url('/areas/listContentAjax')}";
ajaxSectionsUrl 		= "{$html->url('/areas/listSectionAjax')}";
ajaxSectionObjectUrl 	= "{$html->url('/areas/loadSectionAjax')}";
//-->

{if !empty($object)}

	{literal}
	$(document).ready(function() {

		$(".publishingtree #pub_{/literal}{$object.id}{literal} A:first").click();
		
	});
	{/literal}
	
{else}

	{literal}
	$(document).ready(function() {
	
		$(".publishingtree H2:first A").click();
	
	});
	{/literal}

{/if}
</script>


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=false}

<div class="head">

	<h1>{t}Publication tree{/t}</h1>

</div> 

{assign_concat var="actionForm" 1="save" 2=$formToUse|capitalize|default:"Area"}

<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">

<div id="loading" style="position:absolute; left:320px; top:110px; ">&nbsp;</div>

<div class="main full" style="display:none">

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
			<td rel="areacontentC">{t}contents{/t}</td>
			<td rel="areasectionsC">{t}sections{/t}</td>
			<td rel="areapropertiesC">{t}properties{/t}</td>
		</table>	
		<!-- -->	
		
		<div class="htabcontainer" id="sectiondetails">
			
			<div id="areacontentC" class="htabcontent">
					
				&nbsp;
					
			</div>
		
			<div id="areasectionsC" class="htabcontent">
								
				&nbsp;
		
			</div>
			
			
			<div id="areapropertiesC" class="htabcontent">
					
				&nbsp;
		
			</div>

		</div>
		
		<div class="insidecol" style="margin-top:0px; padding-top:0px; margin-left:8px">
		
		<input class="bemaincommands" style="display:inline" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" style="display:inline" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
		
		</div>
	
	</fieldset>	

</div>

</form>
{*bedev}
{$view->element('menuright')}
{/bedev*}
