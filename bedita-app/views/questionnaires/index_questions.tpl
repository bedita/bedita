
</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="indexQuestions"}

{include file="inc/menucommands.tpl" method="indexQuestions" fixed = true}

{include file="../common_inc/toolbar.tpl" itemName="questions"}

<div class="mainfull">
	
	{include file="./inc/list_all_questions.tpl"}

</div>