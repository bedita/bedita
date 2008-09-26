{$javascript->link("jquery/jquery.form", false)}

{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}


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
	$(".main").show();
	$(".tab:first").click();
	$("#sectionTitle").text("{/literal}{$object.title|truncate:42:'…':true}{literal}");
});
{/literal}

{/if}
</script>


</head>


{if !empty($smarty.get.hyper)}

	<body onload="init()">

{else}

	<body>

{/if}

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" fixed=false}

<div class="head">
		
	<h1>{t}Publishing tree{/t}</h1>

</div> 



{assign_concat var="actionForm" 0="save" 1=$formToUse|capitalize|default:"Area"}


<div class="main" style="display:none">

<form action="{$html->url('/areas/')}{$actionForm}" method="post" name="updateForm" id="updateForm" class="cmxform">
				
	<div class="tab"><h2>{t}Details{/t} of &nbsp; <span class="graced" style="font-size:1.5em" id="sectionTitle"></span></h2></div>
	
	<fieldset style="padding:0px" id="properties">		
		
		<!-- <h2 id="sectionTitle" style="margin-bottom:5px"></h2> -->
	
		<div id="loading" style="clear:both">&nbsp;</div>
		
		<ul class="htab">
			<li rel="areacontentC">{t}contents{/t}</li>
			<li rel="areasectionsC">{t}sections{/t}</li>
			<li rel="areapropertiesC">{t}properties{/t}</li>
		</ul>				
		
		<div class="htabcontainer" id="sectiondetails">
			
			<div id="areacontentC" class="htabcontent" style="clear:none">
					
					{include file="inc/list_content_ajax.tpl"}
					
			</div>
		
			<div id="areasectionsC" class="htabcontent" style="clear:none">
								
					{include file="inc/list_sections_ajax.tpl"}
		
			</div>
			
			
			<div id="areapropertiesC" class="htabcontent" style="clear:none">
					
					{assign_concat var=formFile 0="inc/form_" 1=$formToUse|default:"area" 2=".tpl"}	
					{include file=$formFile}
		
			</div>
		</div>
		
		<div style="margin-left:8px">
		
			<input class="bemaincommands" style="display:inline" type="button" value=" {t}Save{/t} " name="save" />
			<input class="bemaincommands" style="display:inline" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		
		</div>
	
	</fieldset>	

</div>

</form>


{include file="../common_inc/menuright.tpl"}












