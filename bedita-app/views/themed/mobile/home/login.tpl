<div data-role="page">

	<div data-role="header">
		<h1>BEdita - Login</h1>
	</div><!-- /header -->

	<div data-role="content">	
	<form action="{$html->url('/authentications/login')}" method="post" name="loginForm" id="loginForm" class="cmxform" style="padding-left:5px;">
		{$beForm->csrf()}
		<input type="hidden" name="data[login][URLOK]" value="{$beurl->here()|escape}" id="loginURLOK" />
		
		<label class="block" id="luserid" for="userid">{t}Username{/t}</label>
		<input class="big" tabindex="1" type="text" name="data[login][userid]" id="userid" class="{literal}{ required:true}{/literal}" title="{t}Username is required{/t}"/>
		<label class="block" id="lpasswd" for="passwd">{t}Password{/t}</label>
		<input class="big" tabindex="2" type="password" name="data[login][passwd]" id="passwd" class="{literal}{ required:true}{/literal}" title="{t}Password is required{/t}"/>
		
		<input class="bemaincommands" tabindex="2" type="submit" value="{t}Enter{/t}"/>
	</form>
	</div><!-- /content -->
</div><!-- /page -->