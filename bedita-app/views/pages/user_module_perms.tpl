<ul class="bordered singola" style="margin-top:100px">
{if !empty($BEAuthUser.userid)}
	<li>{t}User{/t}: <span class="on">{$BEAuthUser.realname}</span></li>
	{if isset($module_modify)}
		<li>{t}Permission{/t}: 
			{if $module_modify eq '1'}
				{t}Modify{/t}
			{else}
				{t}Read{/t}
			{/if}
		</li>
	{/if}
{/if}
	<li>
	{foreach key=key item=item name=l from=$conf->langsSystem}
		<a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a>
		<br />
	{/foreach}
	</li>
	
	<li><a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a></li>
	
</ul>