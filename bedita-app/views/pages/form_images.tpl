<fieldset>
	{if !empty($images)}
	{section name="i" loop=$images}
		{assign var="imageFile" 	value=$images[i].path}
		{assign var="imageTitle" 	value=$images[i].title}
	<div class="{if $smarty.section.i.index%5 eq '0'}imageBoxFirst{else}imageBox{/if}">
		<input type="text" name="data[priority]" value="{$images[i].priority}" class="priority" size="3" maxlength="3"/>
		<div style="width:160px; height:160px; overflow:hidden;">		
		{if !empty($imageFile)}
			{thumb 
				file="$imageFile"
				width="160" height="160"
				linkurl="$imageFile"
				cache="$cache_path"}
		{else}
			<img src="{$session->webroot}/img/image-missing.jpg" width="160"/>
		{/if}
		</div>
		<br/>
		{t}Title{/t}:<br/>
		<input type="text" style="width:160px" value="{$images[i].title}" name="data[title]"/>
		<br/>
		{t}Description{/t}:<br/>
		<textarea style="height:75px; width:160px" name="data[shortDesc]">{$images[i].shortDesc}</textarea>
		<br/>
		{t}Size{/t}:<br/>
		{$images[i].size/1000} Kb
		<br/>
		x: {$images[i].width} y: {$images[i].height}
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		<label for="delete[{$images[i].id}]" class="iumd">{t}Delete{/t}</label> <input type="checkbox" id="delete[{$images[i].id}]" name="data[delete]" value="$images[i].id"/>
		</div>
	</div>
		{assign var="newImagePriority" 	value=$images[i].priority+1}
	{/section}
	{else}
		{assign var="newImagePriority" 	value=1}
	{/if}
	
	<div class="imageBox">
		
		<input type="text" name="data[priority]" value="{$newImagePriority}" class="priority" size="3" maxlength="3"/>
		<div style="width:160px; height:160px; overflow:hidden;">		
		</div>
		<br/>
		{t}Title{/t}:<br/>
		<input type="text" style="width:160px" name="data[title]"/>
		<br/>
		{t}Description{/t}:<br/>
		<textarea style="height:75px; width:160px" name="data[shortDesc]"></textarea>
		<br/>
		{t}Size{/t}:<br/>
		- Kb
		<br/>
		x: - y: -
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		<a href="{$html->url('/images')}/index/keepThis:true/TB_iframe:true/height:480/width:640" title="{t}New Image{/t}" class="thickbox">{t}Add image{/t}</a>
		</div>
	</div>
</fieldset>