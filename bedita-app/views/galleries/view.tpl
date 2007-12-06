{agent var="agent"}
{$html->css('module.galleries')}
{$html->css("jquery.calendar")}
{$html->css("jquery.thickbox")}
{if ($agent.IE)}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("interface")}
{$javascript->link("module.galleries")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.calendar")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}
{$javascript->link("jquery.thickbox")}
<script type="text/javascript">
<!--

{literal}

$(document).ready(function(){

	$('#properties').show() ;

	// aggiunge i comandi per i blocchi
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;

	// handler cambiamenti dati della pagina
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;

});


$(document).ready(function(){
	designTreeWhere() ;
	addCommandWhere() ;
});


// Crea o refresh albero
function designTreeWhere() {
	$("#treeWhere").Treeview({
		control: "#treecontrol" ,
		speed: 'fast',
		collapsed:false
	});
}

// Aggiunge il radio button
function addCommandWhere() {
	$("li/span[@class='SectionItem'], li/span[@class='AreaItem']", "#treeWhere").each(function(i) {
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;

		if(parents.indexOf(parseInt(id)) > -1) {
			$(this).before('<input type="checkbox" name="data[destination][]" value="'+id+'" checked="checked"/>&nbsp;');
		} else {
			$(this).before('<input type="checkbox" name="data[destination][]" value="' +id+'"/>&nbsp;');
		}

		$(this).html('<a href="javascript:;">'+$(this).html()+'</a>') ;

		$("a", this).bind("click", function(e) {
			// Indica l'avvenuto cambiamento dei dati
			try { if(!$("../../input[@type=checkbox]", this).get(0).checked) $().alertSignal() ; } catch(e) {}

			$("../../input[@type=checkbox]", this).get(0).checked = true ;
		}) ;

	}) ;
}

{/literal}

//-->
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="form.tpl"}
</div>