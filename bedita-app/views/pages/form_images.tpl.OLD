<fieldset>
{assign var="thumbWidth" 		value=100}
{assign var="thumbHeight" 		value=100}
{assign var="thumbCache" 		value=$CACHE}
{assign var="thumbPath"         value=$MEDIA_ROOT}
{assign var="thumbBaseUrl"      value=$MEDIA_URL}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}

{if !empty($images)}
	{section name="i" loop=$images}
	
	{assign var="imagePath" 	value=$images[i].path}
	{assign var="imageFile" 	value=$images[i].filename}
	{assign var="imageTitle" 	value=$images[i].title}
	{assign var="newImagePriority" 	value=$images[i].priority+1}
		
	<div class="{if $smarty.section.i.index%5 eq '0'}imageBoxFirst{else}imageBox{/if}">
		<input type="text" name="data[priority]" value="{$images[i].priority}" class="priority" size="3" maxlength="3"/>
		<span>{$imageFile}</span>
		<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
		{if !empty($imageFile)}
			{thumb 
				width="$thumbWidth" 
				height="$thumbHeight" 
				file=$imagePath
				cache="$thumbCache" 
				MAT_SERVER_PATH=$thumbPath 
				MAT_SERVER_NAME=$thumbBaseUrl
				linkurl="$thumbBaseUrl/img/$imageFile"
				longside="$thumbLside"
				shortside="$thumbSside"
				html="$thumbHtml"
				dev="$thumbDev"} 
		{else}
			<img src="{$session->webroot}/img/image-missing.jpg" width="160"/>
		{/if}
		</div>
		<br/>
		{t}Title{/t}:<br/>
		<input type="text" style="width:{$thumbWidth}px" value="{$imageTitle}" name="data[title]"/>
		<br/>
		{t}Description{/t}:<br/>
		<textarea style="height:75px; width:{$thumbWidth}px" name="data[shortDesc]">{$images[i].shortDesc}</textarea>
		<br/>
		{t}Size{/t}:<br/>
		{$images[i].size/1000} Kb
		<br/>
		x: {$images[i].width} y: {$images[i].height}
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		<label for="delete[{$images[i].id}]" class="iumd">{t}Delete{/t}</label> <input type="checkbox" id="delete[{$images[i].id}]" name="data[delete]" value="$images[i].id"/>
		</div>
	</div>
	{/section}
	{else}
		{assign var="newImagePriority" 	value=1}
	{/if}
	<div class="imageBox">		
		<input type="text" name="data[priority]" value="{$newImagePriority}" class="priority" size="3" maxlength="3"/>
		<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
		</div>
		<br/>
		{t}Title{/t}:<br/>
		<input type="text" style="width:{$thumbWidth}px" name="data[title]"/>
		<br/>
		{t}Description{/t}:<br/>
		<textarea style="height:75px; width:{$thumbWidth}px" name="data[shortDesc]"></textarea>
		<br/>
		{t}Size{/t}:<br/>
		- Kb
		<br/>
		x: - y: -
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		{*		<a href="{$html->url('/images')}/index/keepThis:true/TB_iframe:true/height:480/width:640/modal:true" title="{t}New Image{/t}" class="thickbox">{t}Add image{/t}</a> *}
		<a href="{$html->url('/images')}/index/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}New Image{/t}" class="thickbox">{t}Add image{/t}</a>
		</div>
	</div>
</fieldset>