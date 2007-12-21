{literal}
<style type="text/css">
	H1 {float:left;}
	#footerPage {width:auto; margin:0px !important;}
</style>
{/literal}
</head>
<body>
<div id="headerPageHome">
	<div class="beditaButtonHome" onClick = "document.location ='{$html->url('/')}'">
		<span style="font:bold 17px Verdana">B.Edita</span><br/><b>&gt;</b>
		<a href="{$html->url('/authentications/logout')}">esci</a><br/><br/><p>
		<b>Consorzio BEdita</b>
		<br/>2007</p>
	</div>

{section name="m" loop=$moduleList}
	{if ($moduleList[m].status == 'on')}
		{if ($moduleList[m].flag & BEDITA_PERMS_READ) }
			{assign_concat var='linkPath' 0=$html->url('/') 1=$moduleList[m].path}
			{assign var = "link" value=$html->url($linkPath)}

	<h1 class="{$moduleList[m].path}" style="background-color:{$moduleList[m].color};" onClick="document.location='{$html->url('/')}{$moduleList[m].path}'">
	<a href="{$html->url('/')}{$moduleList[m].path}/" style="color:white;">{t}{$moduleList[m].label}{/t}</a>
	</h1>
		{else}
	<div class="gest_menuxHome" style="background-color:#DDDDDD;">{t}{$moduleList[m].label}{/t}</div>
		{/if}
	{/if}
{/section}
</div>
{include file="../messages.tpl"}
<div id="centralPageHome">
</div>