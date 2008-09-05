{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}

{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("validate.tools", false)}

{$javascript->link("jquery/jquery.autogrow")}

<script language="JavaScript">
	{literal}
	$(document).ready( function ()
	{
		$('#properties').show() ;
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
	});
	{/literal}
</script>

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="viewArea"}

<div class="head">
		
	<h2>{t}Tree of Areas{/t}</h2>

</div> 

{include file="inc/menucommands.tpl" method="viewArea" fixed=true}

{assign var='object' value=$area}

<div class="main">
	<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	{include file="inc/form_area.tpl"}
	
</div>


