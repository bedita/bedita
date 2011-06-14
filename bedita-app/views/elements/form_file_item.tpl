{*
** media item in the object container's detail page
*}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
{if empty($relation)}
	{$relation="attach"}
{/if}

{assign_concat var="linkUrl"            1=$html->url('/multimedia/view/') 2=$item.id}

{assign_concat var="imageAltAttribute"	1="alt='"  2=$item.title 3="'"}

{assign_associative var="params" presentation="thumb" width=$thumbWidth height=$thumbHeight longside=false mode="fill" modeparam="000000" type=null upscale=false}
{assign_associative var="htmlAttr" alt=$item.title title=$item.name}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$(".info_file_item").change(function() {
		$(this).parents(".multimediaitem").css("background-color","gold").find(".mod").val(1);
	})
});
</script>
{/literal}


{strip}
	
	<input type="hidden" class="media_nickname" value="{$item.nickname}" />

	<input type="hidden" name="data[RelatedObject][{$relation}][{$item.id}][id]" value="{$item.id}" />
	
	<input type="hidden" class="mod" name="data[RelatedObject][{$relation}][{$item.id}][modified]" value="0" />
	
	<div style="width:{$thumbWidth}px; height:{$thumbHeight}px" class="imagebox">
	{if strtolower($item.ObjectType.name) == "image"}

		{$beEmbedMedia->object($item,$params,$htmlAttr)}
		
	{elseif ($item.provider|default:false)}
	
		{assign_concat var="myStyle" 1="width:" 2=$conf->media.video.thumbWidth 3="px; " 4="height:" 5=$conf->media.video.thumbHeight 6="px;"}
		{assign_associative var="attributes" style=$myStyle}
		{$beEmbedMedia->object($item,$params,$attributes)}
	
	{elseif strtolower($item.ObjectType.name) == "audio"}
	
		<a href="{$linkUrl}"><img src="{$session->webroot}img/iconset/88px/audio.png" /></a>	
	
	{else}

		{$beEmbedMedia->object($item, $params)}
	
	{/if}
	
	</div>
	

	
	<label class="evidence">
		<input type="text" class="priority" style="text-align:left; margin-left:0px;"
		name="data[RelatedObject][{$relation}][{$item.id}][priority]" value="{$item.priority|default:$priority|default:1}" size="3" maxlength="3"/>
	</label>


	<ul class="info_file_item">
		<li>
			<input class="info_file_item" style="border:0px;" type="text" value="{$item.title|escape:'htmlall':'UTF-8'|default:""}" 
			name="data[RelatedObject][{$relation}][{$item.id}][title]" />
		</li>
		<li>
			<textarea class="info_file_item" style="width:100%; border:0px; border-bottom:0px solid silver;" 
			name="data[RelatedObject][{$relation}][{$item.id}][description]">{$item.description|default:""}</textarea>
			<br />
			<table style="width:100%; margin-top:5px" class="ultracondensed">
				<tr>
					<td><a title="info" rel="{$linkUrl} .multimediaiteminside" style="padding:2px 6px 2px 6px !important" class="BEbutton modalbutton">{t}info{/t}</a></td>
					<td><a title="edit" href="{$linkUrl}" style="padding:2px 6px 2px 6px !important" class="BEbutton" target="_blank">{t}edit{/t}</a></td>		
					<td><a title="remove" href="javascript: void(0);" style="padding:2px 6px 2px 6px !important" class="BEbutton" onclick="removeItem('item_{$item.id}')">{t}x{/t}</a></td>
				</tr>
			</table>
		</li>
	</ul>


{/strip}
