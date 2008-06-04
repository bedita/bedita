<div class="modulesmenucaption"> 
	go to: &nbsp;<a>be</a>
</div>

<ul class="modulesmenu">

{section name="m" loop=$moduleList} 
	
	{assign_concat var='linkPath' 0=$html->url('/') 1=$moduleList[m].path}
	<li href="{$linkPath}" title="{t}{$moduleList[m].label}{/t}" class="{$moduleList[m].path} {if (stripos($html->here, $moduleList[m].path) !== false)} on{/if}"></li>
    
{/section}

    <li href="{$html->url('/')}" title="Bedita3 main dashboard" class="bedita"></li>

</ul>