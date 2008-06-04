{*
** galleries view template
** @author ChannelWeb srl
*}

{$html->css('tree')}
{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/jquery.dimensions")}
{$javascript->link("jquery/ui/ui.tabs")}
{$javascript->link("jquery/jquery.autogrow")}
{$javascript->link("form")}
{$javascript->link("jquery/jquery.treeview")}
{$javascript->link("jquery/jquery.changealert")}
{$javascript->link("jquery/jquery.form")}
{$javascript->link("jquery/jquery.selectboxes.pack")}
{$javascript->link("jquery/jquery.cmxforms")}
{$javascript->link("jquery/jquery.metadata")}
{$javascript->link("jquery/jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("jquery/interface")}


</head>
<body>
	


{include file="modulesmenu.tpl" method="view"}	

{include file="inc/menuleft.tpl" method="view"}



<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

{assign var=objIndex value=0}


<form action="{$html->url('/galleries/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />

{include file="inc/menucommands.tpl" fixed=true}



<div class="main">
{include file="inc/form.tpl"}
</div>

