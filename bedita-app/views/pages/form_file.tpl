<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" style="display:block" id="multimediaitem">
<fieldset>
{if (isset($object))}
	<table class="tableForm" border="0">
	{if ($object.ObjectType.name == "image")}
	<tr>
		<td colspan="2">
		{thumb 
			width="100" 
			height="100" 
			file=$imagePath
			cache=$CACHE 
			MAT_SERVER_PATH=$MEDIA_ROOT 
			MAT_SERVER_NAME=$MEDIA_URL
			linkurl=$imageUrl
			longside=""
			shortside=""
			html=""
			dev=""
			offset_w = ""
			sharpen = ""
			addgreytohint = ""	
		} 	
		</td>
	</tr>
	{/if}
	<tr><td class="label">{t}File name{/t}:</td><td>{$object.name|default:""}</td></tr>
	<tr><td class="label">{t}File type{/t}:</td><td>{$object.type|default:""}</td></tr>
	<tr><td class="label">{t}File size{/t}:</td><td>{$object.size|default:""}</td></tr>
	</table>
{/if}
</fieldset>
</div>