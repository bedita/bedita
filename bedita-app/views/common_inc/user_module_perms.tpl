<br style="margin-top:100px" />
{if !empty($BEAuthUser.userid)}
{t}User{/t}: <span class="on">{$BEAuthUser.realname}</span>
{/if}

<ul class="bordered singola" style="margin-top:10px">
	
{*if !empty($conf->multilang) && $conf->multilang}
	<li>
	{foreach key=key item=item name=l from=$conf->langsSystem}
		<a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a>
		<br />
	{/foreach}
	</li>
{/if*}	

	<li><a href="{$html->url('/')}">› {t}Home{/t}</a></li>
{if !empty($BEAuthUser.userid)}
	<li><a href="{$html->url('/authentications/logout')}">› {t}Exit{/t}</a></li>
{/if}
</ul>

{include file="../common_inc/colophon.tpl"}

<div id="handlerChangeAlert"></div>