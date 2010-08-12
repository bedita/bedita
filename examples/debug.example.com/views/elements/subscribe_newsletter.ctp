<?php if (!empty($mailgroups)):?>
	<form action="<?php e($html->url('/hashjob/newsletter_subscribe'));?>" method="post">
		Email: <input type="text" name="data[newsletter_email]" size="30"/>
		<br/>Newsletter(s):
		<?php foreach ($mailgroups as $key => $i):?>
		<br/><input type="checkbox" name="data[joinGroup][<?php e($key);?>][mail_group_id]" value="<?php e($i["MailGroup"]["id"])?>"/> <?php e($i["MailGroup"]["group_name"])?>
		<?php endforeach;?>
		<br/><input type="submit" value="<?php __("subscribe");?>"/>
	</form>
<?php else:?>
	<?php __("No newsletter to subscribe in");?>
<?php endif;?>	