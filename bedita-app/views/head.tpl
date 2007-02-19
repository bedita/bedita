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
           {*if ($moduleList[m].status)}
               {if ($moduleList[m].allowed)}
                       <div class="gest_menux"
                   style="background-color:{$moduleList[m].color}; color: white; "
                   onMouseOver     = "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleList[m].color}'"                          onMouseOut      = "this.style.backgroundColor = oldBGColor"
                   >
                   {assign var='menuItemPath' value=$moduleList[m].path}
                   {assign var='menuItemLabel' value=$moduleList[m].label}
                   {assign_associative var='menuLinkStyle' style="background-color:$moduleList[m].color;color:white"}
                   {if (stripos($bevalidation->here, $moduleList[m].path) !== false)}
                       <i> * {$html->link($menuItemLabel, $menuItemPath, $menuLinkStyle)}</i>
                       {else}
                       {$html->link($menuItemLabel, $menuItemPath, $menuLinkStyle)}
                       {/if}
                   </div>
               {else}
                       <div class="gest_menux" style="background-color:#DDDDDD; color: white; ">
                   {$moduleList[m].label}
                   </div>
               {/if}
           {/if*}
        
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