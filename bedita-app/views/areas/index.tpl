{$javascript->link("jquery/jquery.treeview", true)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/ui/ui.core.min", false)}
{$javascript->link("jquery/ui/ui.sortable.min", false)}


<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	/*
	designAreaTree() ;

	addCommand() ;
	refreshCommand() ;
	refreshOnClick() ;
	$("span.SectionItem").Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});

	*/
	$("#areacontent").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
	}).css("cursor","move");
		
	$("#areasections").sortable ({
		distance: 20,
		opacity:0.7,
		//update: $(this).reorderListItem
	}).css("cursor","move");


	
});

{/literal}
//-->
</script>


</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="head">
		
	<h2>{t}Publishing tree{/t}</h2>

</div> 

<div class="main" style="width:325px;">

	
{if !empty($smarty.get.hyper)}
	{include file="inc/hypertree.tpl"}
{else}
	{include file="inc/form_tree.tpl"}
{/if}

</div>


<div style="width:360px; position:absolute; top:160px; left:640px">

				
<div class="tab"><h2>{t}Section details{/t}</h2></div>

<fieldset style="padding:0px" id="properties">		
	
	<h2 style="margin-bottom:5px">Nome sezione di cui stiamo vedendo il dettaglio</h2>
	
	<ul class="htab">
		<li rel="areacontentC">contenuti</li>
		<li rel="areasectionsC">sezioni</li>
		<li rel="areapropertiesC">prorietà</li>
	</ul>				
	
	<div id="areacontentC" class="htabcontent" style="clear:none">
			
			{include file="inc/list_content_ajax.tpl"}
			
	</div>

	<div id="areasectionsC" class="htabcontent" style="clear:none">
						
			{include file="inc/list_sections_ajax.tpl"}

	</div>
	
	
	<div id="areapropertiesC" class="htabcontent" style="clear:none">
						
			{include file="inc/form_section_ajax.tpl"}

	</div>
	
	
</fieldset>	
							
</div>















