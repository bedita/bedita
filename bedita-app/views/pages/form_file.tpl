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
			cache=$conf->imgCache 
			MAT_SERVER_PATH=$conf->mediaRoot 
			MAT_SERVER_NAME=$conf->mediaUrl
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
	{else}
    <tr>
        <td colspan="2">
	       <a href="{$conf->mediaUrl}{$object.path}" target="_blank">
           <img src="{$session->webroot}img/mime/{$object.type}.gif" />
           </a>
        </td>
    </tr>
    {/if}
	<tr><td class="label">{t}File name{/t}:</td><td>{$object.name|default:""}</td></tr>
	<tr><td class="label">{t}File type{/t}:</td><td>{$object.type|default:""}</td></tr>
	<tr><td class="label">{t}File size{/t}:</td><td>{math equation="x/y" x=$object.size y=1024 format="%d"|default:""} KB</td></tr>
	</table>
{/if}
</fieldset>
</div>