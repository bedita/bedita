{agent var="agent"}

{$html->css('module.area')}
{if ($agent.IE)}
	{$html->css('jquery.ie.autocomplete')}
{else}
	{$html->css('jquery.autocomplete')}
{/if}

{$javascript->link("form")}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}

<script type="text/javascript">
<!--
var parent_id 	= {$parent_id} ;
var current_id	= {$section.id|default:0} ;

{literal}

/* ****************************************************
Albero per selezionare la collocazione della sezione
**************************************************** */
$(document).ready(function(){
	designTree() ;
	addCommand() ;

//	$("#debug").val($("#tree").parent().html()) ;
});

$(document).ready(function(){

	$('#properties').show() ;
	if(!current_id) $('#whereto').show() ;

	// aggiunge i comandi per i blocchi
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;

	// handler cambiamenti dati della pagina
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;

});


// Crea o refresh albero
function designTree() {
	$("#tree").Treeview({
		control: "#treecontrol" ,
		speed: 'fast',
		collapsed:false
	});
}

// Aggiunge il radio button
function addCommand() {
	$("li/span[@class='SectionItem'], li/span[@class='AreaItem']", "#tree").each(function(i) {
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;

		// Se l'elemento da inserire e' il corrente, non inserisc ei comandi
		if(current_id == id) return ;

		if(id != parent_id) {
			$(this).before('<input type="radio" name="data[destination]" value="'+id+'"/>&nbsp;');
		} else {
			$(this).before('<input type="radio" name="data[destination]" value="'+id+'" checked="checked"/>&nbsp;');
		}
		$(this).html('<a href="javascript:;">'+$(this).html()+'</a>') ;

		$("a", this).bind("click", function(e) {
			// Indica l'avvenuto cambiamento dei dati
			try { if(!$("../../input[@type=radio]", this).get(0).checked) $().alertSignal() ; } catch(e) {}

			$("../../input[@type=radio]", this).get(0).checked = true ;
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
{include file="submenu.tpl" method="viewSection"}
{assign var='object' value=$section}
{include file="form_section.tpl"}
</div>