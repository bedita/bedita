tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mce",
	plugins : "safari,pagebreak,paste,fullscreen,advlink,xhtmlxtras",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist, hr, | ,link,unlink,pastetext,pasteword, | ,removeformat,charmap,code,fullscreen",
	theme_advanced_buttons2 : "mybutton,sub, sup, cite, abbr, acronym, del, attribs",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address,dt,dd,code,samp",
	width : "100%",
	
	remove_redundant_brs : true,
	entity_encoding : "raw", //All characters will be stored in non-entity form except these XML default entities: &amp; &lt; &gt; &quot;
	
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/"
});

tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mceSimple",
	plugins : "safari,paste,fullscreen,advlink",
// Theme options
	theme_advanced_buttons1 : "bold,italic,link,unlink,pastetext,removeformat,charmap,code,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "bottom",
	theme_advanced_toolbar_align : "right",
	//theme_advanced_statusbar_location : "bottom",
	width : "100%",
	remove_redundant_brs : true,
	entity_encoding : "raw", //All characters will be stored in non-entity form except these XML default entities: &amp; &lt; &gt; &quot;
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/",
	toolbar_align : "right",
	force_br_newlines : true,
    forced_root_block : '' // Needed for 3.x
});

