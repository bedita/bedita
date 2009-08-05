<div class="commentform">

	{if ($msg->check('Message.error'))}
		<a name="error"></a>
		<div class="message error">
			{$msg->userMsg('error')}
		</div>
	{/if}
	
	<h3>{t}Write a comment{/t}</h3>
	
	<form action="{$html->url('/saveComment')}" method="post">
		<input type="hidden" name="data[object_id]" value="{$section.currentContent.id}" />
	<fieldset>	
		<label>{t}name{/t}</label><br/>
		<input type="text" name="data[author]" style="width:240px;"/><br/><br/>
		<label>{t}email{/t}</label><br/>
		<input type="text" name="data[email]" style="width:240px;"/><br/><br/>
		<label>{t}web site{/t}</label><br/>
		<input type="text" name="data[url]" value="http://" style="width:240px;"/><br/><br/>
		<label>{t}location{/t}</label><br/>
		<input type="text" class="text" name="data[GeoTag][0][address]" style="width:240px;"><br/><br/>
		<label>{t}text{/t}</label><br/>
		<textarea rows="10" cols="50" name="data[description]"></textarea><br/><br/>

		<img src="{$html->url('/captchaImage')}" style="margin-right: 8px;" /><br/>
		<label>{t}Write the text you see in the image above{/t}</label><br/>
		<input type="text" name="captcha" id="captcha" style="width: 240px; margin: 6px 0;"  /> 

	</fieldset>
	
		<input type="submit" value="{t}send comment{/t}" style="margin: 6px 0;" />
	</form>
	
</div>