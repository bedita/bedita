{*
file incluso.
Visualizza il menu e il comando di login/logout
*}
{strip}
<div style="width:1000px;">
                <div class="beditaButton" onClick = "document.location ='/'">
                        <b style="font:bold 17px Verdana">B.Edita </b>
                        <br>
                        {if ($BEAuthAllow)}<b>&#155;</b>{$html->link('Esci', '/Users/logout')}{/if}
                        <br>
                        <p>
                        {$smarty.now|date_format:"%d/%m/%Y"}
                        </p>
                </div>

        {section name="m" loop=$moduleList}
        	{if ($moduleList[m].status)}
        		{if ($moduleList[m].allowed)}
       		 		<div class="gest_menux" 
					style="background-color:{$moduleList[m].color}; color: white; "
    	            onClick = "document.location ='/{$moduleList[m].path}/'"
        	        onMouseOver     = "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleList[m].color}'"        
            	    onMouseOut      = "this.style.backgroundColor = oldBGColor"
                	> 
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