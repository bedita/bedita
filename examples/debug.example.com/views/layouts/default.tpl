{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$beFront->lang()}" dir="ltr">
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>
	{$beFront->metaAll()}
	{$beFront->metaDc()}

	<link rel="icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />

	{$beFront->feeds()}
	
	{$html->css('base')}

	{$javascript->link('jquery')}

	{$scripts_for_layout}

	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		$('.open-close-link').click(function(){
				$(this).next('div').toggle();
			}
		);
	}
	);
	{/literal}
	</script>
</head>

<body>

{$content_for_layout}

{$beFront->stats()}
</body>
</html>