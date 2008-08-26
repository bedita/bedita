{$javascript->link("jquery/jquery.selectboxes.pack", false)}

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

<div class="main">
	<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	{include file="inc/form_section.tpl"}
	</form>
</div>





