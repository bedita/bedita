<?php 
if (empty($object) && !empty($section["currentContent"])) {
	$object = $section["currentContent"];
}

if ($object["comments"] != "off"):?>

	<?php if (!empty($object["Comment"])):?>
	
		<?php foreach ($object["Comment"] as $key => $comment):?>
		
			<?php if ($key == 0):?>
				<h3 style="margin-top:30px;"><?php e(count($object["Comment"]));?>&nbsp;
				<?php 
				if (count($object["Comment"]) == 1):
					__("Comment", false);
				else:
					__("Comments", false);
				endif;
				?>
				</h3>
			<?php endif; ?>
			
			<a name="comment-{$comment.id}"></a>
			<div class=commentContainer>
				<h3>
				<?php if (!empty($comment["url"])):?>
					<a href="{$comment.url}" target="_blank"><?php e($comment["author"]);?></a>
				<?php else:?>
					<?php e($comment["author"]);?>
				<?php endif;?>
				<?php if (!empty($comment["GeoTag"][0]["address"])):?>
					<?php e(" (".$comment["GeoTag"][0]["address"].")");?>
				<?php endif;?>
			 	</h3>
			 	
			 	<p><?php e( strftime($conf->datePattern, strtotime($comment["created"])) );?></p>
				
				<p><?php e( nl2br($comment["description"]) );?></p>
			</div>
			
		<?php endforeach;?>
			
	<?php endif;?>

	<?php if (!empty($showForm)):?>
		<?php e($this->element('form_comments'));?>
	<?php endif;?>
	
<?php endif;?>