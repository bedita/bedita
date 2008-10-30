</head>

<body class="home">


<ul class="modules">

    <li class="bedita" href="{$html->url('/')}">BEdita 3.0</li>
	

{foreach name=module1 from=$moduleList key=k item=mod}
	{if ($mod.status == 'on')}
		{if ($mod.flag & BEDITA_PERMS_READ) }
			{assign_concat var='linkPath' 0=$html->url('/') 1=$mod.path}
			<li class="{$mod.name}" rel="{$linkPath}">{t}{$mod.label}{/t}</li>
		{else}
			<li class="{$mod.name} off" rel="{$linkPath}">{t}{$mod.label}{/t}</li>
		{/if}
	{/if}
	
	{if $smarty.foreach.module1.iteration == 2}
	
	<li class="welcome">
		<h1>welcome</h1>
		andrea alberti
		<br  />
		you have 3 ipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut
	</li>
	
	{/if}
	
{/foreach}
	

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
		<li>uande lingues coalesce, li gramm</li>
		<li>olypian quarrels et gor</li>
		<li>illa congolium sic ad nauseum</li>
		<li>ignitus carborundum e  unum.</li>
		<li>Defacto lingo non provisio</li>
	
	</ul>
	
	

<div class="tab"><h2>{t}search{/t}</h2></div>
	<div style="padding:0px 10px 0px 10px">
		<form>
			<label class="block" for="searchstring">{t}search string:{/t}</label>
			<input type="text" name="searchstring" />
			&nbsp;<input type="submit" value="go" />
			<hr />
		</form>
	</div>



<div class="tab"><h2>{t}all recent items{/t}</h2></div>
	<ul class="bordered">
		<li>uande lingues coalesce, li grammat</li><li>ica del resultant lingue es plu simplic</li><li> e regulari quam ti del coalescent lingues. Li nov lingua franca va esser pl</li><li>u simplic e regulari quam li existent Europan lingues</li><li> sequitur condominium facile et geranium incognito.</li> <li>Epsum factorial non deposit quid pro quo hic escorol. Marquee selectus non provisio incongruous feline nolo contendre</li> <li>Olypian quarrels et gorilla congolium sic ad nauseum</li> <li>ignitus carborundum e pluribus unum</li>
		<li>Li Europan lingues es membres del sam familie</li>
	</ul>


<div class="tab"><h2>{t}connected user{/t}</h2></div>
	<ul class="bordered">
		<li>Marcantonio</li>
		<li>Bruto</li>
		<li>Cassio</li>
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
</from>	
	
	

</div>

	
<p style="clear:both; margin-bottom:20px;" />


{include file="../messages.tpl"}



