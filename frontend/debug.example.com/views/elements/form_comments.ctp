<div class="commentform">

	<?php if (($msg->check('Message.error'))):?>
		<a name="error"></a>
		<div class="message error">
			<?php e($msg->userMsg('error'));?>
		</div>
	<?php endif;?>
	
	<h3><?php __("Write a comment", false);?></h3>
	
	<form action="<?php e($html->url('/saveComment'));?>" method="post">
		<input type="hidden" name="data[object_id]" value="<?php e($section["currentContent"]["id"]);?>" />
	<fieldset>	
	<?php $userLogged = $session->read($conf["session"]["sessionUserKey"]);?>
	<?php if (empty($userLogged)):?>
		<label><?php __("name", false);?></label><br/>
		<input type="text" name="data[author]" style="width:240px;"/><br/><br/>
		<label><?php __("email", false);?></label><br/>
		<input type="text" name="data[email]" style="width:240px;"/><br/><br/>
		<label><?php __("web site", false);?></label><br/>
		<input type="text" name="data[url]" value="http://" style="width:240px;"/><br/><br/>
		<label><?php __("location", false);?></label><br/>
		<input type="text" class="text" name="data[GeoTag][0][address]" style="width:240px;"><br/><br/>
	<?php elseif ( trim($userLogged["email"]) == "" ):?>
		<label><?php __("email", false);?></label><br/>
		<input type="text" name="data[email]" style="width:240px;"/><br/><br/>
	<?php endif;?>
		<label><?php __("text", false);?></label><br/>
		<textarea rows="10" cols="50" name="data[description]"></textarea><br/><br/>

	<?php if (empty($userLogged)):?>
		<img src="<?php e($html->url('/captchaImage'));?>" style="margin-right: 8px;" /><br/>
		<label><?php __("Write the text you see in the image above", false);?></label><br/>
		<input type="text" name="captcha" id="captcha" style="width: 240px; margin: 6px 0;"  /> 
	<?php endif;?>
	</fieldset>
	
		<input type="submit" value="<?php __("send comment", false);?>" style="margin: 6px 0;" />
	</form>
	
</div>