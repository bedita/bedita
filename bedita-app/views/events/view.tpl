{*
** events view template
** @author ChannelWeb srl
*}


{$html->css("ui.datepicker")}

{$javascript->link("jquery/ui/datepicker/ui.datepicker", false)}

{$javascript->link("jquery/ui/datepicker/ui.datepicker")}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/datepicker/ui.datepicker-$currLang.js")}
{/if}


</head>
<body>
	
{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

<form action="{$html->url('/events/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{include file="../common_inc/form_common_js.tpl"}
{assign var=objIndex value=0}
<input  type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
	
{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="main">
		
{include file="inc/form.tpl"}	

</div>

</form>



{include file="../common_inc/menuright.tpl"}


