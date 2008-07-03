{*
** multimedia view template
** @author ChannelWeb srl
*}

{literal}
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#title').show() ;
	$('#multimediaitem').show() ;
	$('textarea.autogrowarea').css("line-height","1.2em").autogrow();
});
//-->
</script>
{/literal}
</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

{include file="inc/menucommands.tpl" method="view" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>

{include file="../common_inc/menuright.tpl"}
