
<div class="standardreport">
	
	
	<div class="modules" style="float:left"><label class="bedita">{$conf->userVersion}</label></div>
	<div class="modules" style="float:left"><label class="project">{$conf->projectName|default:''}</label></div>
	
	<br style="clear:both" />
	
	<h1>{$object.title|default:'<i>no title</i>'}</h1>

<ul>
{foreach from=$object key=k item=v}
   <li>
   		<label>{$k}:</label> 
		
		{if !(is_array($v))} 
		
			{$v} 
		
		{else}
		
			<ul>
				{foreach from=$v key=kk item=vv}
					<li>
						<label>{$kk}:</label>
						
						{if !(is_array($vv))} 
							{$vv} 
						{else}
						<ul>
							{foreach from=$vv key=kkk item=vvv}
								<li>
									<label>{$kkk}:</label>
										{$vvv}
								</li>
							{/foreach}
						</ul>
						{/if}
			
					</li>
				{/foreach}
			</ul>
		
		{/if}
   
   </li>
{/foreach}
</ul>

</div>

{*
<pre>
{dump var=$object}
*}