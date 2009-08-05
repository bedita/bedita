<h3>Language: <?php echo $currLang; ?></h3>
<h3>Frontend: [title] <?php echo $publication['title']; ?> - [id] <?php echo $publication['id']; ?> </h3>

<?php e($this->element('form_search'));?>

<h3>Sections Tree</h3>
<pre>
<?php print_r($sectionsTree); ?>
</pre>