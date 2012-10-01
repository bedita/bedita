tinyMCE.init({
	
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mcea",
	plugins : "safari,pagebreak,paste,fullscreen",

	// Theme options
	theme_advanced_buttons1 : "sub, sup, bold,italic,underline,strikethrough, charmap, fontsizeselect , pastetext, pasteword ,link,unlink, code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "", 
	theme_advanced_toolbar_location : "bottom",
	theme_advanced_toolbar_align : "right",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
	width : "550",

	extended_valid_elements : 'input[accept|accesskey|align<bottom?left?middle?right?top|alt|autocomplete|autofocus|checked<checked|class|dir<ltr?rtl|disabled<disabled|form|id|ismap<ismap|lang|list|max|maxlength|min|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect|pattern|placeholder|readonly<readonly|required<required|size|src|style|tabindex|title|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text?datetime?datetime-local?date?month?time?week?number?range?email?url?search?tel?color|usemap|value]',
	
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/",

	force_br_newlines : true,
	forced_root_block : '' // Needed for 3.x,

});