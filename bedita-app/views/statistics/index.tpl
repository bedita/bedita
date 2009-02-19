
{$javascript->link("form", false)}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
		
		openAtStart("#objects");

		
    });
</script>
{/literal}


</head>

<body>


{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
	{if !empty($sectionSel)} 
		<h2>{$moduleName} for “ <span style="color:white" class="evidence">{$sectionSel.title}</span> ”</h2>
	{/if}
	</div>

</div>

<div class="main">

{include file="inc/statsobjects.tpl"}

{include file="inc/statseditors.tpl"}

{include file="inc/statsusers.tpl"}

</div>