<h3><?php __("sections tree");?>: $sectionsTree</h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide");?></a>
<div style="display: none">
<?php pr($sectionsTree); ?>
</div>

<?php if (!empty($feedNames)): ?>
<h3><?php __("feeds available");?>: $feedNames</h3>
<ul>
<?php foreach ($feedNames as $feed): ?>
	<li><a href="<?php $html->url('/rss/'); e($feed["nickname"]);?>"><?php e($feed["title"]);?></a></li>
<?php endforeach; ?>
</ul>
<?php endif;?>