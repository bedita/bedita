{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}

<script type="text/javascript">
<!--
var current_id	= {$section.id|default:0} ;
{literal}
$(document).ready(function(){
	$('#title').show() ;
	$('#properties').show() ;
	if(!current_id) $('#whereto').show() ;
});
{/literal}
//-->
</script>

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="viewSection"}

<div class="head">
		
	<h2>{t}Section{/t}</h2>

</div> 

{include file="inc/menucommands.tpl" method="viewSection"}

{assign var='object' value=$section}

<div class="main">

	{include file="inc/form_section.tpl"}
	
</div>





