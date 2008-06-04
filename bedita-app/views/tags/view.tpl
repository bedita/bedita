{$html->css('tree')}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;
});

{/literal}
//-->
</script>

</head>

<body>

{include file="../modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">

	<h2>
		{if $object}
			Tag	“<em style="color:#FFFFFF; line-height:2em">{$object.label}</em>”
		{else}
			{t}New tag{/t}
		{/if}
	</h2>
	
</div>

<form action="{$html->url('/tags/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	
{include file="inc/menucommands.tpl" method="view" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

</form>

