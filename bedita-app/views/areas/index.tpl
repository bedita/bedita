{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/ui/ui.core.min", false)}
{$javascript->link("jquery/ui/ui.sortable.min", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}


<script type="text/javascript">
<!--

/* define urls for ajax calls. Used in module.areas.js */
ajaxContentsUrl = "{$html->url('/areas/listContentAjax')}";
ajaxSectionsUrl = "{$html->url('/areas/listSectionAjax')}";
ajaxSectionObjectUrl = "{$html->url('/areas/loadSectionAjax')}";
//-->

{if !empty($section)}

{literal}
$(document).ready(function() {
	$(".tab:first").click();
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
		
	<h2>{t}Publishing tree{/t}</h2>

</div> 

<div class="main" style="left:180px; width:420px;">

	
{if !empty($smarty.get.hyper)}
	{include file="inc/hypertree.tpl"}
{else}
	{include file="inc/form_tree.tpl"}
{/if}

</div>

<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">

<div style="width:420px; position:absolute; top:160px; left:580px">

				
<div class="tab"><h2>{t}Section details{/t}</h2></div>

<fieldset style="padding:0px" id="properties">		
	
	<h2 id="sectionTitle" style="margin-bottom:5px"></h2>

	<div id="loading" style="clear:both">&nbsp;</div>
	
	<ul class="htab">
		<li rel="areacontentC">{t}contents{/t}</li>
		<li rel="areasectionsC">{t}sections{/t}</li>
		<li rel="areapropertiesC">{t}properties{/t}</li>
	</ul>				
	
	<div id="areacontentC" class="htabcontent" style="clear:none">
			
			{include file="inc/list_content_ajax.tpl"}
			
	</div>

	<div id="areasectionsC" class="htabcontent" style="clear:none">
						
			{include file="inc/list_sections_ajax.tpl"}

	</div>
	
	
	<div id="areapropertiesC" class="htabcontent" style="clear:none">
						
			{include file="inc/form_section.tpl"}

	</div>
	
	<hr />
	<input class="bemaincommands" style="display:inline" type="button" value=" {t}Save{/t} " name="save" />	
	<input class="bemaincommands" style="display:inline" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />

	
</fieldset>	
							
</div>

</form>















