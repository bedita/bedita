
<div class="galleryC">

	{assign var="item" value=$section.currentContent.relations.attach}
	{section name=g loop=$item}
	
	{assign_associative var="paramsBig" width=680 mode="fill" upscale=false URLonly=true}

	<div class="thumb" {if $smarty.section.g.iteration is div by 3}style="margin-right:0px;"{/if}>
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
	{if $smarty.section.g.iteration is div by 3}
	<br style="clear:both;" />
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


