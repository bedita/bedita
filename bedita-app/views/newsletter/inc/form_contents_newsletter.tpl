{if ($conf->mce|default:true)}
	{$javascript->link("tiny_mce/tiny_mce")}
{literal}
<script language="javascript" type="text/javascript">

tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mce",
	plugins : "safari,pagebreak,paste,fullscreen",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist, hr, | ,link,unlink,pastetext,pasteword, | ,removeformat,charmap,code,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "", 
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
	width : "470",

	
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/"
	
	

});

	</script>
{/literal}
{/if}


<div class="tab"><h2>{t}Compile{/t}</h2></div>

<fieldset id="contents">


	<label>{t}Title{/t}: </label>
	{assign_concat var="default" 0="Newsletter | " 1=$smarty.now|date_format:"%B %Y"}
	<input type="text" id="title" name="data[title]" 
	value="{$object.title|default:$default|escape:'html'|escape:'quotes'}" id="titleBEObject"/>
	
	<hr />

	<label>template :</label>
	<select>
				<option value="">--</option>
				<option>list of all templates</option>
				<option>grouped by publishing</option>
			</select>
	
	&nbsp;&nbsp;
		<input class="modalbutton" type="button" value="{t}Get contents{/t}" rel="{$html->url('/areas/showObjects/')}{$rel}" style="width:200px" />
	
	<hr />
	
	<textarea name="data[body]" style="height:400px" class="mce">{$object.body|default:''}
	qui ci va il testo e se si pigia "get contents" qui sopra, si 'appende' via ajax
	anche il contenuto tratto dagli oggetti selezionati nella modale.
	COME e cosa si prenda da quegli oggetti dipende dal template in uso.
	ma in sostanza titolo, immagine, testobreve e/o descrizione).
	Non penso di tenere la relazione tra l'oggetto e la newsletter, in sostanza la scelta
	degli oggetti serve solo come helper per riempire la textarea di testo, 
	che il redattore potrà modificaRE COME GLI PARE.
	Dal template dipendono anche titolo, firma(appesa anch'essa alla fine del testo) 
	e grafica of course.
	I templates a loro volta dipendono dalla pubblicazione da cui traggono 
	l'URL per il dettaglio delle notizie, per l'iscrizione e la disiscrizione, il css etc
	</textarea>

</fieldset>
