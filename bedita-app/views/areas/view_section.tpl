{$html->css('tree')}
{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/jquery.dimensions", false)}
{$javascript->link("jquery/ui/ui.tabs", false)}
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
	if(!current_id) $('#whereto').show() ;
	$('.showHideBlockButton').bind("click", function(){ $(this).next("div").toggle() ; }) ;
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;
});
{/literal}
//-->
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="viewSection"}
{assign var='object' value=$section}
{include file="form_section.tpl"}
</div>