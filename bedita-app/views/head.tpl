{*
file incluso.
Visualizza il menu e il comando di login/logout
*}
{strip}
<div style="width:1000px;">
                <div class="beditaButton" onClick = "document.location ='/'">
                        <b style="font:bold 17px Verdana">B.Edita </b>
                        <br/>
                        {if ($BEAuthAllow)}<b>&#8250;</b>{$html->link('Esci', '/users/logout')}{/if}
                        <br/>
                        <p>
                        {$smarty.now|date_format:"%d/%m/%Y"}
                        </p>
                </div>

        {section name="m" loop=$moduleList}        
        	{if ($moduleList[m].status)}
        		{if ($moduleList[m].allowed)}
        		{assign_concat var='linkPath' 0="/" 1=$moduleList[m].path}
        		{assign var = "link" value=$html->url($linkPath)}
        			<div class="gest_menux" 
					style="background-color:{$moduleList[m].color}; color: white; "
    	            onClick = "document.location ='{$link}'"
        	        onMouseOver     = "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleList[m].color}'"        
            	    onMouseOut      = "this.style.backgroundColor = oldBGColor"
                	> 
       		 		{if ( strcmp($moduleList[m].path, $beurl->controllerName()) === 0 )} 
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