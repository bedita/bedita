{*
Template Home page.
*}
{php}
$vs = &$this->get_template_vars() ;
// pr($vs["moduleList"]);
// exit;
{/php}

</head>
<body>
<div id="container">
	<div id="header">
		{include file="head.tpl"}
	</div>
	<div id="content">
		{if ($session->check('Message.flash'))}{$session->flash()}{/if}
	</div>
</div>