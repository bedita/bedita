<h3><?php __("current language", false); echo ": " . $currLang ." - " . __("other available", true);?>:
<?php foreach ($conf->frontendLangs as $k => $g): ?>
	<?php if ($currLang != $k): ?>
		<a title="<?php e($g[1]); ?>" href="<?php echo $html->url('/lang') . "/" . $k?>"><?php e($k)?> - <?php e($g[1]);?></a>
	<?php endif; ?>
<?php endforeach; ?>
</h3>

<hr/>
<h3><?php echo __("current locale", true);?>: <?php echo $currLocale;?></h3>
<em><?php echo strftime("%A %e %B", time());?></em>

<hr/>
<h3><?php __("menu", false);?>:</h3>
<a href="javascript:void(0)" class="open-close-link"><?php echo __("show/hide", true);?></a>
<div style="display:none;">
<?php echo $beFront->menu($sectionsTree); ?>
</div>

<hr/>
<h3><?php __("section breadcrumb", false);?>:</h3>
<?php echo $beFront->breadcrumb(); ?>
<br/>

<?php if (!empty($section["currentContent"])): ?>
<hr/>
<h3><?php __("current content", false); ?>: <a href="<?php $html->url($section["currentContent"]["canonicalPath"]);?>"><?php e($section["currentContent"]["title"]);?></a></h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
<div style="display: none">
<?php pr($section["currentContent"]); ?>
</div>
<?php endif; ?>

<?php if (!empty($section["childSections"])): ?>
<hr/>
<h3><?php __("sections in this section");?>: $section.childSections</h3>
<ul>
	<?php foreach ($section["childSections"] as $subsection): ?>
		<li><a href="<?php $html->url($subsection["canonicalPath"]);?>"><?php e($subsection["title"]);?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($section["childContents"])): ?>
<hr/>
<h3><?php __("contents in this section");?>: $section.childContents</h3>
<ul>
	<?php foreach ($section["childContents"] as $object): ?>
		<li><a href="<?php $html->url($object["canonicalPath"]);?>"><?php e($object["title"]);?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

