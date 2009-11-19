{$view->element('header')}

<div class="main">
{$view->element('menuLeft')}

<div class="center modulo5">
	<div class="intro">{$intro.description}</div>
	
	
		
		{if !empty($progetti[0].relations.attach)}
		<div class="imgHome">
			{assign var="item" value=$progetti[0].relations.attach[0]|default:''}
			{assign_associative var="params" width=240 height='' mode="fill" upscale=false URLonly=false}
			{$beEmbedMedia->object($item,$params)}
		</div>
		{/if}
		
		{if !empty($progetti[1].relations.attach)}
		<div class="imgHome">
			{assign var="item" value=$progetti[1].relations.attach[0]|default:''}
			{assign_associative var="params" width=225 height='' mode="fill" upscale=false URLonly=false}
			{$beEmbedMedia->object($item,$params)}
		</div>
		{/if}
		
		{if !empty($progetti[2].relations.attach)}
		<div class="imgHome">
			{assign var="item" value=$progetti[2].relations.attach[0]|default:''}
			{assign_associative var="params" width=160 height='' mode="fill" upscale=false URLonly=false}
			{$beEmbedMedia->object($item,$params)}
		</div>
		{/if}
		
		{if !empty($progetti[3].relations.attach)}
		<div class="imgHome">
			{assign var="item" value=$progetti[3].relations.attach[0]|default:''}
			{assign_associative var="params" width=305 height='' mode="fill" upscale=false URLonly=false}
			{$beEmbedMedia->object($item,$params)}
		</div>
		{/if}
		
		{if !empty($progetti[4].relations.attach)}
		<div class="imgHome">
			{assign var="item" value=$progetti[4].relations.attach[0]|default:''}
			{assign_associative var="params" width=160 height='' mode="fill" upscale=false URLonly=false}
			{$beEmbedMedia->object($item,$params)}
		</div>
		{/if}
		

	

</div>

</div>

		



{$view->element('footer')}

