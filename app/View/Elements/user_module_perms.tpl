
<div class="insidecol" style="margin-top:50px; padding-top:5px; padding-bottom:5px; border-top:5px solid gray; border-bottom:5px solid gray;">

{if !empty($BEAuthUser.userid)}
{t}User{/t}: <span class="on">{$BEAuthUser.realname}</span>
{/if}

<ul class="bordered" style="border-top:1px solid gray; border-bottom:1px solid gray; padding:2px 0px 0px 0px; margin:10px 0px 10px 0px">

	<li style="padding-left:0px"><a href="{$this->Html->url('/')}">› {t}Home{/t}</a></li>
	{if !empty($BEAuthUser.userid)}
		<li style="padding-left:0px"><a href="{$this->Html->url('/authentications/logout')}">› {t}Exit{/t}</a></li>
	{/if}

</ul>

{$view->element('colophon')}

<ul class="bordered" style="border-top:1px solid gray; border-bottom:1px solid gray; padding:2px 0px 0px 0px; margin:10px 0px 10px 0px">
	{foreach key=key item=item name=l from=$conf->langsSystem}	
	<li style="padding-left:0px"><a {if $this->Session->read('Config.language') == $key}class="on"{/if} href="{$this->Html->base}/lang/{$key}">› {$item}</a></li>
	{/foreach}
</ul>

<div id="handlerChangeAlert"></div>

</div>


