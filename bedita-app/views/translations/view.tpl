{*
** translations view template
*}


</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	{if !empty($object_translation.title)}<h1>{$object_translation.title|default:'<i>[no title]</i>'}</h1>{/if}
	{t}translation of{/t}
	<h1 style="margin-top:0px">{$object_master.title|default:'<i>[no title]</i>'}</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="mainfull">	

	{include file="inc/form.tpl"}

</div>


{*include file="../common_inc/menuright.tpl"*}