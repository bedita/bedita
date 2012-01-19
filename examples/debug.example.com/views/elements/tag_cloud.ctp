<?php if (!empty($listTags)):?>
	<h2><?php __('Tag cloud', false);?></h2>

	<?php foreach ($listTags as $tag):?>
		<a title="<?php echo $tag['weight'];?>" class="tagCloud <?php echo (!empty($tag['class']))? $tag['class'] : "";?>" href="<?php echo $html->url('/tag/' . $tag['name'])?>"><?php echo $tag['label']?></a>
	<?php endforeach;?>
<?php endif;?>