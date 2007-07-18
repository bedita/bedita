{*
file incluso.
Visualizza il menu e il comando di login/logout
*}
{strip}

<div id="headerPage">
	<div class="beditaButton" onClick = "document.location ='/'">
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
               <div class="gest_menux" 
			style="background-color:{$moduleList[m].color}; color: white; "
    	          onClick = "javascript:document.location ='{$link}'"
        	     onMouseOver     = "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleList[m].color}'"        
            	onMouseOut      = "this.style.backgroundColor = oldBGColor">
       		 	{if (stripos($bevalidation->here, $moduleList[m].path) !== false)} 
       		 		<i> * {$moduleList[m].label}</i>
       		 		{else}
       		 		{$moduleList[m].label}
       		 	{/if}
                	</div>
                {else}
       		 		<div class="gest_menux" style="background-color:#DDDDDD; color: white; ">
                    {$moduleList[m].label}
                	</div>
                {/if}
              
            {/if}
        {/section}
</div>

{/strip}