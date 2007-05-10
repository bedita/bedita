{*
file incluso.
Visualizza il menu e il comando di login/logout
*}

{literal}
<script type="text/javascript">
	modulesColor = new Array();
	$(document).ready(function() {
		$("div.gest_menux").click(function() {
			location.href= "{/literal}{$html->url("/")}{literal}" + $(this).attr("id");;
		})
		
		$("div.gest_menux").hover(function() {
			oldBGColor = $(this).css("background-color");
			$(this).css("background-color", modulesColor[$(this).attr("id")]);
		}, function() {
			$(this).css("background-color", oldBGColor);
		})
	});
</script>
{/literal}

{strip}
<div style="width:1000px;margin-bottom:0px">
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
        			<div class="gest_menux" id="{$moduleList[m].path}"
						 style="{if ( strcmp($moduleList[m].path, $beurl->controllerName()) === 0 )}
									background-color:{$moduleList[m].color}; 
								{/if}	
								color: white; ">
	       		 		{$moduleList[m].label}
	       		 		<script type="text/javascript">
	       		 			modulesColor["{$moduleList[m].path}"] = "{$moduleList[m].color}";
	       		 		</script>
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