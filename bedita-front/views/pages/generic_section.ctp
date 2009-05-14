<h2>Default template</h2>

<?php 
e($this->element('show_comments', array(
		'object' => $section['currentContent'],
		'showForm' => true
		)
	)
);
?>	

<pre><?php print_r($section); ?></pre>