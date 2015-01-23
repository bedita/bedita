<div class="standardreport">
	
	<div class="modules" style="float:left"><label class="bedita">BEdita {$conf->version}<br /><br />{$conf->projectName|default:''}</label></div>
	<div class="modules" style="float:left">
		<label class="{$object.ObjectType.module_name|default:''}">
			{$object.ObjectType.name|default:''}
			<!-- <input type="button" onClick="print()" value="print me" style="margin:3	0px 0px 0px 30px" class="BEbutton"> -->
		</label>
	</div>
	
	<br style="clear:both" />
	<h1>{$object.title|escape|default:'<i>no title</i>'}</h1>
	
<ul>
{foreach from=$object key=k item=v}
   <li>
   		<label>{t}{$k}{/t}:</label> 

		{if !(is_array($v))} 
		
			{$v} 
		
		{else}
		
			<ul>
				{foreach name="second" from=$v key=k2 item=v2}
					<li {if ($smarty.foreach.second.index == 0)}style="border:0px solid silver"{/if}>
						<label>{t}{$k2}{/t}:</label>
						
						{if !(is_array($v2))} 
							{$v2} 
						{else}
						<ul>
							{foreach from=$v2 key=k3 item=v3}
								<li>
									<label>{t}{$k3}{/t}:</label>
										{if !(is_array($v3))} 
											{$v3} 
										{else}
										<ul>
											{foreach from=$v3 key=k4 item=v4}
												<li>
													<label>{t}{$k4}{/t}:</label>
														{if !(is_array($v4))} 
															{$v4} 
														{else}
														<ul>
															{foreach from=$v4 key=k5 item=v5}
																<li>
																	<label>{t}{$k5}{/t}:</label>
																		{$v5}
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
	<script type="text/javascript">
		print();
	</script>