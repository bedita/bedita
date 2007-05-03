<div class="gest_menuLeft" style="width:auto; margin:0px !important;">

	<div class="beditaButton" style="height:136px; margin-left:-1px; margin-bottom:0px;" onClick = "document.location ='/'">
		<b style="font:bold 17px Verdana">B.Edita</b>
		{if ($BEAuthAllow)}<br/><b>&#8250;</b>&nbsp;{$html->link('esci', '/users/logout')}{/if}
        <br/><br/>
        <p>
        {$smarty.now|date_format:"%d/%m/%Y"}
        </p>
	</div>

	{section name="m" loop=$moduleList}        
    	{if ($moduleList[m].status)}
    		{if ($moduleList[m].allowed)}
    		{assign_concat var='linkPath' 0="/" 1=$moduleList[m].path}
    		{assign var = "link" value=$html->url($linkPath)}
    			<h1 style="background-color:{$moduleList[m].color}; color: white; float: left;"
	            onClick = "document.location ='{$link}'"
    	        onMouseOver     = "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleList[m].color}'"        
        	    onMouseOut      = "this.style.backgroundColor = oldBGColor"
            	>
            	{$moduleList[m].label}
                </h1> 
            {/if}
        {/if}
    {/section}

</div>


