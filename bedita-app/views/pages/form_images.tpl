<fieldset>
	<input type="submit" value="{t}Add image{/t}"/>
	<br/>
	<br/>
	{if !empty($images)}
	{section name="i" loop=$images}
	<div class="{if $smarty.section.i.index%5 eq '0'}imageBoxFirst{else}imageBox{/if}">
		<input type="text" value="{$images[i].priority}" style="font-weight:bold;font-size:20px;" size="3" maxlength="3"/>
		<img src="{$images[i].path}" style="width:140px;border:solid #000 1px"/>
		<br/><input type="text" value="{$images[i].title}"/>
		<br/>{t}Size{/t}: {$images[i].size/1000} Kb
		<br/><input type="button" value="{t}Delete{/t}"/>		
	</div>
	{/section}
	{else}
	{t}No images found{/t}
	{/if}
</fieldset>