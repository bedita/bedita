{*
Pagina d'entrata modulo Documents.
*}
{php}$vs = &$this->get_template_vars() ;{/php}
{agent var="agent"}
{$html->css('tree')}
{$html->css('module.documents')}
{$html->css("jquery.calendar")}
{$html->css("jquery.thickbox")}

{if ($agent.IE)}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}
{$javascript->link("module.documents")}
{$javascript->link("interface")}
{$javascript->link("jquery.calendar")}
{$javascript->link("jquery.thickbox")}

<script type="text/javascript">
<!--

{* Avoid Javascript errors when a document have no 'parents' *}
{if is_array($parents) && count($parents) > 1}
	var parents = new Array({section name=i loop=$parents}{$parents[i]}{if !($smarty.section.i.last)},{/if}{/section}) ;

{elseif is_array($parents) && count($parents) == 1}
	var parents = new Array() ;
	parents[0] = {$parents[0]} ;
{else}
	var parents = new Array() ;
{/if}

{literal}
/* ****************************************************
Albero per selezionare la collocazione della sezione
**************************************************** */

$(document).ready(function(){

	$('#properties').show() ;	
	$('#attachments').show() ;
	
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
	$("span[@class='SectionItem'], span[@class='AreaItem']", "#treeWhere").each(function(i) {
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
		
		if(parents.indexOf(parseInt(id)) > -1) {
			$(this).before('<input type="checkbox" name="data[destination][]" id="s_'+id+'" value="'+id+'" checked="checked"/>&nbsp;');
		} else {
			$(this).before('<input type="checkbox" name="data[destination][]" id="s_'+id+'" value="'+id+'"/>&nbsp;');			
		}
		
		$(this).html('<label class="section" for="s_'+id+'">'+$(this).html()+"<\/label>") ;
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