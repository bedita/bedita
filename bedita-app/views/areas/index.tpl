{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
<script type="text/javascript">
<!--
{literal}

$(document).ready(function(){
	
	designAreaTree() ;

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
				$(this).after("<ul><\/ul>");
				subbranch = $('ul', this.parentNode);
			}

			subbranch.eq(0).append(dropped.parentNode);

			// Resetta l'albero
			resetTree() ;
			designAreaTree() ;
			refreshCommand() ;
			refreshOnClick() ;

			// Indica l'avvenuto cambiamento dei dati
			try { $().alertSignal() ; } catch(e) {}
		}
	}) ;

	// handler cambiamenti dati della pagina
	$("#handlerChangeAlert").changeAlert($('input, textarea, select')) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div, #containerPage a, #containerPage span').alertUnload() ;

	// formatta i dati da salvare
	$("#frmTree").bind("submit", function() {
		tree = submitTree("#tree") ;
		$("#data_tree", "#frmTree").val(tree.toString()) ;
		return true ;
	}) ;

//$("#debug").val($("#tree").parent().html()) ;

	// localize up/down buttons
	var buttonUpArr = document.getElementsByName('up');
	var buttonDownArr = document.getElementsByName('down');
	for(i=0;i<buttonUpArr.length;i++) { buttonUpArr[i].value = '{/literal}{t}up{/t}{literal}'; }
	for(i=0;i<buttonDownArr.length;i++) { buttonDownArr[i].value = '{/literal}{t}down{/t}{literal}'; }

});

{/literal}
//-->
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="form_tree.tpl" method="index"}

</div>