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
		
	$(".pubmodule").sortable ({
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


		{include file="inc/form_tree.tpl"}


</div>


<div style="width:325px; position:absolute; top:180px; left:640px">

	<a href="{$html->url('viewSection/')}"><h2>"Programma"</h2></a>					
	<hr >
	<ul class="htab">
		<li rel="areacontentC">contenuti</li>
		<li rel="areapropertiesC">prorietà</li>
	</ul>				
	
	<div id="areacontentC" class="htabcontent" style="clear:none">
		<ul style="margin-top:10px; display: block;" id="areacontent" class="bordered">
			{section name=m loop=14}
			<li>
				<input type="text" class="priority" 
				style="text-align:right; margin-left: -30px; margin-right:10px; width:35px; float:left; background-color:transparent" 
				name="" value="{$smarty.section.m.iteration}" size="3" maxlength="3"/>
		
				<span class="listrecent documents" style="margin-left:0px">&nbsp;&nbsp;</span>
				<a title="2008-05-20 10:28:54" href="/documents/view/691">Nasce la Ctv</a>
				
			</li>
			{/section}
		</ul>		
		<hr>	
		<a href="#" class="graced" style="font-size:3em">‹ ›</a>
	</div>
	
	<div id="areapropertiesC" class="htabcontent" style="clear:none">					
			{*include file="inc/form_section.tpl"*}					
			<a href="{$html->url('viewArea/')}">› prorietà della sezione</a>
			<br>
			<a href="{$html->url('viewArea/')}">› contenuti della sezione</a>
	</div>
								
</div>















