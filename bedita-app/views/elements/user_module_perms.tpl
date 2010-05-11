
<div id="editors" class="insidecol"></div>

<div class="insidecol" style="margin-top:50px; padding-top:5px; padding-bottom:5px; border-top:5px solid gray; border-bottom:5px solid gray;">


{if !empty($BEAuthUser.userid)}
{t}User{/t}: <span class="on">{$BEAuthUser.realname}</span>
{/if}

<ul class="bordered" style="border-top:1px solid gray; border-bottom:1px solid gray; padding:2px 0px 0px 0px; margin:10px 0px 10px 0px">
	
	<li style="padding-left:0px"><a href="{$html->url('/')}">› {t}Home{/t}</a></li>
	{if !empty($BEAuthUser.userid)}
		<li style="padding-left:0px"><a href="{$html->url('/authentications/logout')}">› {t}Exit{/t}</a></li>
	{/if}

</ul>

{$view->element('colophon')}

{if !empty($conf->multilang) && $conf->multilang}
<ul class="bordered" style="border-top:1px solid gray; border-bottom:1px solid gray; padding:2px 0px 0px 0px; margin:10px 0px 10px 0px">
	{foreach key=key item=item name=l from=$conf->langsSystem}	
	<li style="padding-left:0px"><a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a></li>
	{/foreach}
</ul>
{/if}

<div id="handlerChangeAlert"></div>

</div>


