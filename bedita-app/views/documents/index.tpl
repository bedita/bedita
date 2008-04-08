{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("jquery.changealert")}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">	
{include file="submenu.tpl" method="index"}	
{include file="../pages/list_objects.tpl" method="index" areasectiontree=$areasectiontree assocToSections=true}
</div>