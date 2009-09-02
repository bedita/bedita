<div class="standardreport">
	
	<div class="modules" style="float:left"><label class="bedita">{$conf->userVersion}<br /><br />{$conf->projectName|default:''}</label></div>
	<div class="modules" style="float:left">
		<label class="{$object.ObjectType.module|default:''}">
			{$object.ObjectType.name|default:''}
			<!-- <input type="button" onClick="print()" value="print me" style="margin:3	0px 0px 0px 30px" class="BEbutton"> -->
		</label>
	</div>
	
	<br style="clear:both" />
	<h1>{$object.title|default:'<i>no title</i>'}</h1>
	
<ul>
{foreach from=$object key=k item=v}
   <li>
   		<label>{t}{$k}{/t}:</label> 

		{if !(is_array($v))} 
		
			{$v} 
		
		{else}
		
			<ul>
				{foreach name="second" from=$v key=kk item=vv}
					<li {if ($smarty.foreach.second.index == 0)}style="border:0px solid silver"{/if}>
						<label>{t}{$kk}{/t}:</label>
						
						{if !(is_array($vv))} 
							{$vv} 
						{else}
						<ul>
							{foreach from=$vv key=kkk item=vvv}
								<li>
									<label>{t}{$kkk}{/t}:</label>
										{if !(is_array($vvv))} 
											{$vvv} 
										{else}
										<ul>
											{foreach from=$vvv key=kkkk item=vvvv}
												<li>
													<label>{t}{$kkkk}{/t}:</label>
														{$vvvv}
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


{literal}
	<script type="text/javascript">
		print();
	</script>
{/literal}

