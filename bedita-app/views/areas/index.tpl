{*
Pagina d'entrata modulo Areas.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

	{$html->css('module.area')}
	
	{$javascript->link("jquery.treeview.pack")}
	{$javascript->link("interface")}
	{$javascript->link("module.area")}
	{$javascript->link("form")}

<script type="text/javascript">
{literal}

$(document).ready(function(){
	designTree() ;
	addCommand() ;
	refreshCommand() ;
	refreshOnClick() ;
	$("span.SectionItem").Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});
		
	$("span.SectionItem, span.AreaItem").Droppable({
		accept:		'SectionItem',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;
				
			// Sposta
			subbranch = $('ul', this.parentNode);
			
			if (subbranch.size() == 0) {
				$(this).after('<ul></ul>');
				subbranch = $('ul', this.parentNode);
			}
				
			subbranch.eq(0).append(dropped.parentNode);
				
			// Resetta l'albero
			resetTree() ;
			designTree() ;
			refreshCommand() ;
			refreshOnClick() ;
			
		}
	}) ;
});

{/literal}
</script>	
	
</head>
<body>

{include file="head.tpl"}

<div id="centralPage">
	
	{include file="submenu.tpl" method="index"}
	
	{include file="form_tree.tpl" method="index"}
	
</div>
