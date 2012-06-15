<script type="text/javascript">
{literal}
$(document).ready(function(){
	var myPhotoSwipe = $("#gallery a").photoSwipe();
});
{/literal}
</script>

<h1>{$section.currentContent.title}</h1>
<p>Sei in: > <a href="{$html->url($section.canonicalPath)}">{$section.title}<p></a>

<p>{$section.currentContent.body}</p>

{if !empty($section.currentContent.relations.attach.image)}
	<div id="gallery" class="clearfix">
		<h2>GALLERIA DI IMMAGINI</h2>
		{assign_associative var="imgBig" width=900 height=400 mode="crop"}
		{assign_associative var="imgBigAttr" width="100%" style="margin-bottom: 2%;"}
		{assign_associative var="imgSmall" width=400 height=400 mode="crop"}
		{assign_associative var="imgSmallAttr" width="49%"}
		{foreach from=$section.currentContent.relations.attach.image item="image" key="nickname" name="fc_image"}
			<a href="{$conf->mediaUrl}{$image.uri}" title="{$image.title}" rel="external">
			{if $smarty.foreach.fc_image.first}
				{$beEmbedMedia->object($image,$imgBig,$imgBigAttr)}
			{else}
				{if $smarty.foreach.fc_image.iteration % 2 == 0}
					{array_add var="imgSmallAttr" style="float: left; margin-bottom: 2%;"}
				{else}
					{array_add var="imgSmallAttr" style="float: right; margin-bottom: 2%;"}
				{/if}
				{$beEmbedMedia->object($image,$imgSmall,$imgSmallAttr)}
			{/if}
			</a>
		{/foreach}
	</div>
{/if}

{if !empty($section.currentContent.relations.attach.video)}
	<div id="video" class="clearfix">
		<h2>VIDEO</h2>
		{assign_associative var="videoParams" presentation="link" URLonly=true}
		<ul>
		{foreach from=$section.currentContent.relations.attach.video item="video" key="nickname" name="fc_video"}
			<li><a href="{$beEmbedMedia->object($video, $videoParams)}">{$video.title}</a></li>
		{/foreach}
		</ul>
	</div>
{/if}