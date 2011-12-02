<div class="modules pubtab shadow">
	
	<div style="position:absolute; width:10px; top:0px; left:-10px; height:140px; background-color:white;"></div>
	
	<div class="menu">
		<nav>
			<a href="{$html->url('/')}" title="BEdita home" class="index">home</a>
		{foreach from=$moduleList key=k item=mod}
		{if ($mod.status == 'on')}
			{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
			<a href="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name}">{t}{$mod.label}{/t}</a>
		{/if}
		{/foreach}
		
			{foreach from=$publications item=item}
			{if !empty($item.public_url)}<a target="_blank" href="{$item.public_url}" title="{$item.public_name} | {$item.public_url}" class="index" style="padding-right:10px;">
				<img class="smallicon" src="{$html->webroot}img/iconUrl.png"> {$item.public_url|truncate:28}</a>
			{/if}{/foreach}
			<a href="{$html->url('/logout')}" title="Exit" class="index">exit</a>
		</nav> 
	</div>

</div>
