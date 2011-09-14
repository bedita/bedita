<?php echo $html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $beFront->lang(); ?>" dir="ltr">
<head>
	<?php echo $html->charset(); ?>
	<title><?php echo $beFront->title(); ?></title>
	<?php echo $beFront->metaAll();?>
	<?php echo $beFront->metaDc();?>

	<link rel="icon" href="<?php echo $session->webroot; ?>favicon.ico" type="image/x-icon" />
	
	<?php 
	echo $beFront->feeds();
	echo $html->css('base');
	echo $javascript->link('jquery');
	echo $scripts_for_layout;
	?>

	<script type="text/javascript">
	$(document).ready(function() {
		$('.open-close-link').click(function(){
				$(this).next('div').toggle();
			}
		);
	});
	</script>

</head>
<body>
<?php echo $content_for_layout; ?>

<?php echo $beFront->stats(); ?>
</body>
</html>