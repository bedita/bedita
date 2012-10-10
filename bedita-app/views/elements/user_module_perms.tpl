
<div class="insidecol" style="margin-top:30px; padding:0px; border-top:5px solid gray; border-bottom:5px solid gray;">

<ul class="bordered" style="border-top:1px solid gray; border-bottom:1px solid gray; padding:2px 0px 0px 0px; margin:0px 0px 10px 0px">

{if !empty($BEAuthUser.userid)}
	<li style="padding:5px">
		<a href="{$html->url('/profile/')}">{t}User{/t}: <span class="on">{$BEAuthUser.realname}</span></a>
	</li>
{/if}

	<li style="padding:5px"><a href="{$html->url('/')}">{t}Home{/t}</a></li>
	{if !empty($BEAuthUser.userid)}
		<li style="padding:5px"><a href="{$html->url('/profile/')}">{t}Profile{/t}</a></li>
		<li style="padding:5px"><a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a></li>
	{/if}

</ul>

{$view->element('colophon')}

<ul class="bordered" style="border-top:1px solid gray; padding:2px 0px 0px 0px; margin:10px 0px 10px 0px">
	{foreach key=key item=item name=l from=$conf->langsSystem}	
	<li style="padding:5px"><a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a></li>
	{/foreach}
</ul>

<div id="handlerChangeAlert"></div>

</div>


