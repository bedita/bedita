{agent var="agent"}
{$html->css('tree')}
{$html->css('module.documents')}
{$html->css("ui.datepicker")}
{if ($agent.IE)}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$html->css('ui.tabs')}
{$javascript->link("ui/jquery.dimensions")}
{$javascript->link("ui/ui.tabs")}
{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.selectboxes.pack")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("module.documents")}
{$javascript->link("interface")}
{$javascript->link("datepicker/ui.datepicker")}
{if $currLang != "eng"}
	{$javascript->link("datepicker/ui.datepicker-$currLang.js")}
{/if}
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
$(document).ready(function(){
	$('#properties').show() ;
	$('#extendedtext').show() ;
	$('#attachments').show() ;
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;
});

$(document).ready(function(){
	designTreeWhere() ;
	addCommandWhere() ;
});

function designTreeWhere() {
	$("#treeWhere").Treeview({
		control: "#treecontrol" ,
		speed: 'fast',
		collapsed:false
	});
}

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