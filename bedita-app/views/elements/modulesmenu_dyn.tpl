<div class="modules modulesmenu_d shadow">	
	<div style="position:absolute; width:10px; top:0px; left:-10px; height:140px; background-color:white;"></div>
	<nav>
		<ul>
			<li class="index"><a href="{$html->url('/')}" title="BEdita home">home</a></li>
		{if !empty($moduleList)}
		{foreach from=$moduleList key=k item=mod}
		{if ($mod.status == 'on')}
			{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
			<li class="{$mod.name}">
				<a href="{$link}" title="{t}{$mod.label}{/t}" >{t}{$mod.label}{/t}</a>
			</li>
		{/if}
		{/foreach}
		{/if}
		{if !empty($publications)}
		{foreach from=$publications item=item}
			{if !empty($item.public_url)}
			<li class="index"><a target="_blank" href="{$item.public_url}" title="{$item.public_name} | {$item.public_url}">
				<img class="smallicon" src="{$html->webroot}img/iconUrl.png">{$item.public_url|truncate:32:'[…]':true:true}</a></li>
			{/if}
		{/foreach}
		{/if}
			<li class="index"><a href="{$html->url('/logout')}" title="Exit">{t}exit{/t}</a></li>
		</ul> 
	</nav>

</div>
{*
{foreach from=$moduleList key=k item=mod}
{if ($mod.status == 'on')}
<ul class="sub_modulesmenu_d shadow {$mod.name}">
	<li><a href="#">{t}create new document{/t}</a></li>
	<li><a href="#">{t}view all{/t}</a></li>
</ul>
{/if}
{/foreach}
*}