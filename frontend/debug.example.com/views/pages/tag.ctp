
<h1 style="margin-bottom:30px;"><?php __("Objects tagged by", false);?> "<?php e($tag["label"]);?>"</h1>
<?php if (!empty($tag["items"])):?>
	<ul>
	<?php foreach ($tag["items"] as $object):?>
		<li><a href="<?php e($html->url('/') . $object["nickname"]);?>"><?php e($object["title"]);?></a></li>
	<?php endforeach;?>
	</ul>
<?php endif;?>