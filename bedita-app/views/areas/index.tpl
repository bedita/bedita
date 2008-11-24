{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.autogrow", false)}

{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}

<script type="text/javascript">
<!--

/* define urls for ajax calls. Used in module.areas.js */
ajaxContentsUrl 		= "{$html->url('/areas/listContentAjax')}";
ajaxSectionsUrl 		= "{$html->url('/areas/listSectionAjax')}";
ajaxSectionObjectUrl 	= "{$html->url('/areas/loadSectionAjax')}";
//-->

{if !empty($object)}

	{literal}
	$(document).ready(function() {

		$(".publishingtree #pub_{/literal}{$object.id}{literal}").click();
		
	});
	{/literal}
	
{elseif $new}
	{literal}
	
	/// ajax script col form vuoto da mettere
	
	
	{/literal}
{else}

	{literal}
	$(document).ready(function() {
		$(".publishingtree H2:first").click();
	});
	{/literal}

{/if}
</script>



</head>



<body>



{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" fixed=false}

<div class="head">
		
	<h1>{t}Publishing tree{/t}</h1>

</div> 

{assign_concat var="actionForm" 0="save" 1=$formToUse|capitalize|default:"Area"}

<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">

<div id="loading" style="position:absolute; left:320px; top:110px; ">&nbsp;</div>

<div class="main" style="display:none">


	<div class="tab"><h2>{t}Details{/t} of &nbsp; <span class="graced" style="font-size:1.5em" id="sectionTitle"></span></h2></div>
	
	
	
	<fieldset style="padding:0px" id="properties">		
	
		<ul class="htab">
			<li rel="areacontentC">{t}contents{/t}</li>
			<li rel="areasectionsC">{t}sections{/t}</li>
			<li rel="areapropertiesC">{t}properties{/t}</li>
		</ul>				
		
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
		
		<div class="insidecol" style="margin-left:8px">
		
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
		
		</div>
	
	</fieldset>	

</div>

</form>


{include file="../common_inc/menuright.tpl"}

