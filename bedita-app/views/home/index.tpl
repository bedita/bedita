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
	


{foreach name=module1 from=$moduleList key=k item=mod}
	{*if ($mod.status == 'on')*}
        {if ($mod.flag & BEDITA_PERMS_READ) }

            {assign_concat var='linkPath' 0=$html->url('/') 1=$mod.path}
            <li class="{$mod.path}" rel="{$linkPath}">{t}{$mod.label}{/t}</li>
        {else}
            <li class="{$mod.path} off" rel="{$linkPath}">{t}{$mod.label}{/t}</li>
		{/if}
	{*/if*}
	
    {if $smarty.foreach.module1.iteration == 2}
	
	<li class="welcome">
		<h1>welcome</h1>
		{$BEAuthUser.realname}
		<br  />
		you have 3 ipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut
	</li>
	
	{/if}
	
{/foreach}
	

	<li class="colophon" style="width:240px">

		<h2>BEdita</h2>
	
		un software di <strong>Chialab</strong> and <strong>Channelweb</strong>

		<hr />
{if !empty($conf->multilang) && $conf->multilang}
	{foreach key=key item=item name=l from=$conf->langsSystem}
		<a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a>
		<br />
	{/foreach} 
{/if}	
		<hr />
		<a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a>
	</li>
	

	
</ul> 





<div class="dashboard">

<h1>dashboard</h1>



<div class="tab"><h2>{t}your 5 recent items{/t}</h2></div>
	
	<ul class="bordered">
	{section name="n" loop=$lastModBYUser}
		<li><span class="listrecent {$lastModBYUser[n].ObjectType.module}">&nbsp;</span><a title="{$lastModBYUser[n].BEObject.modified}" href="{$html->url('/')}{$lastModBYUser[n].ObjectType.module}/view/{$lastModBYUser[n].BEObject.id}">{$lastModBYUser[n].BEObject.title}</a></li>
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
		<li><span class="listrecent {$lastMod[n].ObjectType.module}">&nbsp;&nbsp;</span>&nbsp;<a title="{$lastMod[n].BEObject.modified}" href="{$html->url('/')}{$lastMod[n].ObjectType.module}/view/{$lastMod[n].BEObject.id}">{$lastMod[n].BEObject.title}</a></li>
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
	
	




{literal}
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{/literal}{$html->url('/tags/listAllTags')}{literal}", function() {
				$("#loadingTags").slideUp("fast");
				$("#listExistingTags").slideDown("fast");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
			} else {
				$("#listExistingTags").slideDown("fast");
			}
			showTags = !showTags;
		}
	});	
});
//-->
</script>
{/literal}

<div class="tab"><h2 id="callTags">{t}tags{/t}</h2></div>
<div>
	<div id="loadingTags" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>
	
	<div id="listExistingTags" class="tag graced" style="display: none; text-align:justify;"></div>
</div>

</div>	
<p style="clear:both; margin-bottom:20px;" />

