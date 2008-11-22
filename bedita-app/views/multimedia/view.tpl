{*
** multimedia view template
** @author ChannelWeb srl
*}

{$javascript->link("jquery/ui/ui.datepicker.min", false)}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
		var openAtStart ="#multimediaitem";
		$(openAtStart).prev(".tab").BEtabstoggle();
    });
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
