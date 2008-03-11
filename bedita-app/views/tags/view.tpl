{$html->css('tree')}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;
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
{include file="submenu.tpl" method="index"}	
{include file="form.tpl"}	
</div>