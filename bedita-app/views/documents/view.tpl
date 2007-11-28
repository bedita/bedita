{*
Pagina d'entrata modulo Documents.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

{agent var="agent"}

{$html->css('module.documents')}
{$html->css("jquery-calendar")}
{if ($agent.IE)} 	{$html->css('jquery.ie.autocomplete')}
{else}				{$html->css('jquery.autocomplete')}
{/if}

{$javascript->link("form")}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}
{$javascript->link("module.documents")}
{$javascript->link("interface")}
{$javascript->link("jquery-calendar")}


<script type="text/javascript">
<!--

var parents = new Array({section name=i loop=$parents}{$parents[i]},{/section} 0) ;
{literal}
/* ****************************************************
Albero per selezionare la collocazione della sezione
**************************************************** */

$(document).ready(function(){

	$('#properties').show() ;
//	$('#whereto').show() ;

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

//	$("#debug").val($("#tree").parent().html()) ;
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
			$(this).before('<input type="checkbox" name="data[destination][]" value="'+id+'"/>&nbsp;');			
		}
		
		$(this).html('<a href="javascript:;">'+$(this).html()+"<\/a>") ;

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


