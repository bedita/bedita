{*
Template Events.
*}
{php}
$vs = &$this->get_template_vars() ;
// pr($vs);
// exit;
{/php}

</head>
<body>
<div id="container">
	
	{include file="head.tpl"}
	
	<div id="content">
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}

	</div>

</div>