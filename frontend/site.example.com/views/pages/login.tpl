{strip}
<div class="top">

	<div class="lang">
	<ul class="footel" style="border:0; margin:0; padding:0;">
		{foreach from=$conf->frontendLangs item="g" key="k"}
			<li>
				<a {if $currLang == $k}style="color:white;"{/if} title="{$g}" href="{$html->url('/')}lang/{$k}">{$g}</a>
			</li>
		{/foreach}

	</ul>
	</div>

	<div class="logo">
		<a title="{$publication.public_name}" href="{$html->url('/')}"><img src="{$html->webroot}img/BElogo24.png" alt="" /></a>
	</div>

	<div class="strillo">
		{$publication.public_name|default:$publication.title}
	</div>
	
	<div class="illustrazione" style="margin-left:30px; ">

		<img src="{$html->webroot}img/albero.png" />
		
	</div>


	<div class="topG"></div>
</div>

<div class="headmenu">
</div>
{/strip}



<div class="main">

	<div class="content-main">
	
		<div class="textC">
			<form action="{$html->here}" method="post">
			<label>username</label>
			<br />
			<input type="text" name="login[userid]" />
			<br />
			<label>password</label>
			<br />
			<input type="password" name="login[passwd]" />
			<br />
			<input type="hidden" name="backURL" value="{$beurl->here()}"/>
			<input style="margin:10px 0px 10px 0px" type="submit" value="{t}submit{/t}" />
			</form>	
		</div>
		
	</div>
	
</div>
