<script type="text/javascript">
<!--
var urlToSearch = "{$html->url('/home/search')}" 
{literal}
function loadSearch() {
	$("#searchResult").load(urlToSearch, {searchstring: $("input[@name='searchstring']").val()}, function() {
			
	});
}

$(document).ready(function() {
	
	$("#searchButton").click(function() {
		loadSearch();
	});
	
	$("input[@name='searchstring']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			loadSearch();
		}
	});
	
});
{/literal}
//-->
</script>

</head>

<body class="home">


<ul class="modules">

    <li class="bedita" rel="{$html->url('/')}">BEdita 3.0</li>
	

{section name="m" loop=$moduleList}
	{if ($moduleList[m].status == 'on')}
		{if ($moduleList[m].flag & BEDITA_PERMS_READ) }
			{assign_concat var='linkPath' 0=$html->url('/') 1=$moduleList[m].path}

			<li class="{$moduleList[m].path}" rel="{$linkPath}">{t}{$moduleList[m].label}{/t}</li>
		{else}
			<li class="{$moduleList[m].path} off" rel="{$linkPath}">{t}{$moduleList[m].label}{/t}</li>
		{/if}
	{/if}
	
	{if $smarty.section.m.iteration == 2}
	
	<li class="welcome">
		<h1>welcome</h1>
		{$BEAuthUser.realname}
		<br  />
		you have 3 ipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut
	</li>
	
	{/if}
	
{/section}
	

	<li class="colophon">

		<h2>BEdita</h2>
	
		<a href="css-class.html">› sed diam nonum</a>
		<br />
		<a href="testinside.html">› nibh euismod</a>
		<br />
		<a href="testinside2.html">› nonummy nibh</a>
		<hr />
		un software di <strong>Chialab</strong> and <strong>Channelweb</strong>
	 	<br />
		<a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a>
	</li>
	

	
</ul> 





<div class="dashboard">

<h1>dashboard</h1>



<div class="tab"><h2>{t}your 5 recent items{/t}</h2></div>
	
	<ul class="bordered">
	{section name="n" loop=$lastModBYUser}
		<li><span class="{$lastModBYUser[n].ObjectType.module}">&nbsp;&nbsp;</span>&nbsp;<a title="{$lastModBYUser[n].BEObject.modified}" href="{$html->url('/')}{$lastModBYUser[n].ObjectType.module}/view/{$lastModBYUser[n].BEObject.id}">{$lastModBYUser[n].BEObject.title}</a></li>
	{/section}
	</ul>
	


<div class="tab"><h2>{t}search{/t}</h2></div>
	<div style="padding:0px 10px 0px 10px">
		<form>
			<label class="block" for="searchstring">{t}search string:{/t}</label>
			<input type="text" name="searchstring" value=""/>
			&nbsp;<input id="searchButton" type="button" value="go" />
			<hr />
		</form>
	<div id="searchResult"></div>
	</div>


<div class="tab"><h2>{t}all recent items{/t}</h2></div>
	<ul class="bordered">
	{section name="n" loop=$lastMod}
		<li><span class="{$lastMod[n].ObjectType.module}">&nbsp;&nbsp;</span>&nbsp;<a title="{$lastMod[n].BEObject.modified}" href="{$html->url('/')}{$lastMod[n].ObjectType.module}/view/{$lastMod[n].BEObject.id}">{$lastMod[n].BEObject.title}</a></li>
	{/section}
	</ul>


<div class="tab"><h2>{t}connected user{/t}</h2></div>
	<ul class="bordered">
	{section name="i" loop=$connectedUser}
		<li>{$connectedUser[i]}</li>
	{/section}
	</ul>
	
<div class="tab"><h2>{t}message board{/t}</h2></div>
<form>
	
	<div class="modulesmiddle">
		<div class="messageboard">
			[ Cassio ]
			Si può fare così, oppure usare un collegamento da qui 
			a un servizio inside messaging già fatto e migliore... 
			twitter? 
			<hr />
			[ Marcantonio ]
			Hey ci siete? Perché avete ucciso il vecchio?
			<hr />
			[ Bruto ]
			Ci stava sulle palle
			<hr />
			[ Cassio ]
			Aveva rotto<br />
			Non ne potevamo più, 'ste menate sul Senato
			<hr />
			[ Bruto ]
			E poi Ottaviano di qua, Ottaviamo di la, eh che palle... neanche fosse suo figlio vero
			<hr />
			[ Marcantonio ]
			E se lo scopava pure, comunque vi vengo a prendere

		</div>
	</div>
	<fieldset style="padding:0px 10px 10px 10px;">
		<label>{t}your message{/t}:</label>
		<textarea style="margin-bottom:5px; height:28px; width:210px;" id="messageboard" name="messageboard"></textarea>
		<input type="submit" value="send" />
	</fieldset>
</form>	
	
	

</div>


{if !empty($conf->multilang) && $conf->multilang}
{t}Language{/t}{if $session->check('Config.language')} [{$session->read('Config.language')}] {/if}:
{foreach key=key item=item name=l from=$conf->langsSystem}
<a href="{$html->base}/lang/{$key}">{$item}</a>{if !$smarty.foreach.l.last} | {/if}
{/foreach} 
{/if}

	
<p style="clear:both; margin-bottom:20px;" />


{include file="../common_inc/messages.tpl"}


