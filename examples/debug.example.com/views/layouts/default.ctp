<?php echo $html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>
	<?php 
		if (!empty($publication['public_name']))
			echo $publication['public_name'];
		else
			echo $publication['title'];
	?>
	<?php if (!empty($section)) echo " | " . $section['title']; ?></title>
	<link rel="icon" href="<php echo $session->webroot; ?>favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<php echo $session->webroot; ?>favicon.ico" type="image/x-icon" />
	<?php echo $html->charset('utf-8');?>
	<meta name="author" content="" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?php 
	if (!empty($feedNames)):
		foreach($feedNames as $feed): ?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo $feed['title']; ?> 
			href="<?php echo $html->url('/rss') . '/' . $feed['nickname']; ?>" />
		<?php endforeach;
	endif; 
	
	echo $scripts_for_layout;
	?>
</head>
<body>
<?php echo $content_for_layout; ?>

<div id="footerPage">
</div>

<?php 
	if (empty($conf->staging) && !empty($publicationz['stats_code'])) 
		echo $publication['stats_code'];
?>
</body>
</html>