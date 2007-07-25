{literal}
<script type="text/javascript">
	$(document).ready(function() {
		$("h1").fadeTo("slow", 0.3);
		$("h1").hover(function() {
			$(this).fadeTo("normal",1);
		}, function() {
			$(this).fadeTo("normal",0.3);
		})
		$("h1").click(function() {
			location.href= "{/literal}{$html->url("/")}{literal}" + $(this).attr("id");
		})
	});
</script>
{/literal}

<div class="gest_menuLeft" style="width:auto; margin:0px !important;">

	<div class="beditaButton" style="height:136px; margin-left:-1px; margin-bottom:0px;" onClick = "document.location ='$html->url('/')'">
		<b style="font:bold 17px Verdana">B.Edita</b>
		{if ($BEAuthAllow)}<br/><b>&#8250;</b>&nbsp;{$html->link('esci', '/authentications/logout')}{/if}
        <br/><br/>
        <p>
        {$smarty.now|date_format:"%d/%m/%Y"}
        </p>
	</div>

	{section name="m" loop=$moduleList}        
    	{if ($moduleList[m].status)}
    		{if ($moduleList[m].allowed)}
    		{assign_concat var='linkPath' 0=$html->url('/') 1=$moduleList[m].path}
    		{assign var = "link" value=$html->url($linkPath)}
    			<h1 style="background-color:{$moduleList[m].color}; color: white; float: left;" id="{$moduleList[m].path}">
            	{$moduleList[m].label}
                </h1> 
            {/if}
        {/if}
    {/section}

</div>