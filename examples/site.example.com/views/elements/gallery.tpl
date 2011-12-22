
<div class="galleryC">

	{assign var="item" value=$section.currentContent.relations.attach}
	{assign var="counter" value=1}
	{assign_associative var="paramsBig" width=680 mode="fill" upscale=false URLonly=true}
	{assign_associative var="htmlVideo"  width=430}
	
	{section name=g loop=$item}

	{if $item[g].object_type_id == $conf->objectTypes.image.id}
	
		<div class="thumb" {if $counter is div by 3}style="margin-right:0px;"{/if}>
			{if !empty($item)}
				{assign_associative var="params" width=135 height=135 mode="crop" upscale=true}
				<a class="thickbox" href="{$beEmbedMedia->object($item[g],$paramsBig)}" 
				title="{$item[g].description}" rel="gallery">
				{$beEmbedMedia->object($item[g],$params)}
				</a>
				<div style="font-size:12px; width:135px;">
					{$item[g].title|truncate:20:'...'}
				</div>
			{/if}
		</div>
		{if $counter is div by 3}
		<br style="clear:both;" />
		{/if}
	
		{math assign="counter"  equation="x+1" x=$counter}
		
	{elseif $item[g].object_type_id == $conf->objectTypes.video.id}
		
		<div style="clear:both; margin-bottom: 10px;">
			{$beEmbedMedia->object($item[g],null,$htmlVideo)}
			<div style="font-size:12px; width:135px;">
				{$item[g].title}
			</div>
		</div>
		{assign var="counter" value=1}
		
	{/if}
	
	{/section}

</div>

<div class="abstract" style="margin-left:20px; padding-top:0px;">

	<h1>{$section.currentContent.title}</h1>
		
	<p class="testo">
		
		{$section.currentContent.body}
		
		{$section.currentContent.abstract}
	
	</p>

	

	{assign var="parents" value=$section.currentContent.relations.parent|default:''}
	<ul>
	{section name="i" loop=$parents}
		<li>
			<a title="{$parents[i].title}" href="{$html->url('/')}{$parents[i].nickname}">
				{$parents[i].title}
			</a>
		</li>
	{/section}
	</ul>
	
	{assign var="links" value=$section.currentContent.relations.link|default:''}
	<ul>
	{section name="i" loop=$links}
		<li>
			<a title="{$links[i].title}" href="{$links[i].url}" target="{$links[i].target|default:'_blank'}">
				{$links[i].title}
			</a>
		</li>
	{/section}
	</ul>
			

</div>


