{*
Template Home page.
*}
{php}$vs = &$this->get_template_vars() ;{/php}
{literal}
<style type="text/css">
	H1 {float:left;}
	#footerPage {width:auto; margin:0px !important;}
</style>
{/literal}
</head>
<body>
<div id="headerPageHome">
	<div class="beditaButtonHome" onClick = "document.location ='/'">
		<b style="font:bold 17px Verdana">B.Edita</b><br><b>&#155;</b> 
		<a href="{$html->url('/authentications/logout')}">esci</a><br><br><p>
		<b>Consorzio BEdita</b>
		<br>2007</p>
	</div>

		{section name="m" loop=$moduleList}
        	{if ($moduleList[m].status == 'on')}
        		{if (($moduleList[m].flag & BEDITA_PERMS_MODIFY) && $moduleList[m].status == 'on')}
	        		{assign_concat var='linkPath' 0='/' 1=$moduleList[m].path}
    	    		{assign var = "link" value=$html->url($linkPath)}

	<h1 class="{$moduleList[m].path}" style="background-color:{$moduleList[m].color};" onClick="document.location='/{$moduleList[m].path}'">
	<a href="{$html->url('/')}{$moduleList[m].path}/" style="color:white;">{$moduleList[m].label}</a>
	</h1>    	    	
                {else}
       		 		<div class="gest_menuxHome" style="background-color:#DDDDDD;">
                    {$moduleList[m].label}
                	</div>
                {/if}
            {/if}
        {/section}
</div>
<div id="centralPageHome">
TESTO HOME O ANCHE NIENTE
</div>

