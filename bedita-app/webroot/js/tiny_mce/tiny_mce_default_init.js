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
	width : "470",
	
	remove_redundant_brs : true,

	
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/"
	
	/*
	// creare un plugin con i comenadi necessari
	setup : function(ed) {
        // Add a custom button
        ed.addButton('mybutton', {
            title : 'Definition',
            image : '../img/iconFuture.png',
            onclick : function() {
				ed.focus();
				ed.selection.setContent('<dfn>' + ed.selection.getContent() + '</dfn>');
            }
        });
    }
    */
	
});

