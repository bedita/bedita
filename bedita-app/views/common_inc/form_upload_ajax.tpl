
<div id="ajaxUploadContainer{$uploadIdSuffix|default:''}" style="display:none; padding:20px 0px 0px 20px;">
	
	
	<table style="margin-bottom:20px">
	<tr>
		<td>{t}file{/t}:</td>
		<td><input style="width:270px;" type="file" name="Filedata{$uploadIdSuffix|default:''}" /></td>
	</tr>
	
	<tr>
		<td>{t}title{/t}:</td>
		<td><input style="width:270px;" type="text" name="streamUploaded{$uploadIdSuffix|default:''}[title]" class="formtitolo" value=""></td>
	</tr>
	
	<tr>
		<td>{t}description{/t}:</td>
		<td><textarea name="streamUploaded{$uploadIdSuffix|default:''}[description]" class="autogrowarea" style="width:270px; min-height:16px; height:16px;"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="button" style="width:160px; margin-top:15px" id="uploadForm{$uploadIdSuffix|default:''}" value="{t}Upload{/t}"/></td>
	</tr>
	</table>

	<a href="javascript:void(0);">{t}Use the multiple upload{/t}</a>

	<div id="msgUpload{$uploadIdSuffix|default:''}"></div>

</div>