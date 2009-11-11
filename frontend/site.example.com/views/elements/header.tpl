{strip}
<div class="top">

	<div class="lang">
	<ul class="footel" style="border:0; margin:0; padding:0;">
		{foreach from=$conf->frontendLangs item="g" key="k"}
			<li>
				<a {if $currLang == $k}style="color:white;"{/if} title="{$g}" href="{$html->url('/')}lang/{$k}">{$g}</a>
			</li>
		{/foreach}

	</ul>
	</div>

	<div class="logo">
		<a title="{$publication.public_name}" href="{$html->url('/')}"><img src="{$html->webroot}img/BElogo24.png" alt="" /></a>
	</div>

	<div class="strillo">
		{$publication.public_name|default:$publication.title}
	</div>
	
	<div class="illustrazione" style="margin-left:30px; ">
		<img src="{$html->webroot}img/albero.png" />
	</div>

	<div class="topG"></div>
</div>

<div class="headmenu">
	
	<ul class="menuP">
	{foreach from=$menu item="m"}
		<li title="{$m.nickname}" 
		{if !empty($section) && ( $section.nickname|default:'' == $m.nickname|default:'' || !empty($section.pathSection[$m.id]) )}
			class="on"
		{/if}>
		<a title="{$m.title}" href="{$html->url($m.canonicalPath)}">{$m.title}</a>
		</li>
	{/foreach}
	</ul>
</div>
{/strip}