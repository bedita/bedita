BEDITA = BEDITA || {};

BEDITA.richtextConf = {
	configFull: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'ShowBlocks' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,bedita,onchange,webkit-span-fix,codesnippet,justify',
		language: BEDITA.currLang2,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true,
		height:660
	},
	
	configNormal: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'paragraph', groups: [ 'list', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find' ], items: [ 'Find' ] },
			{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'tools', items: [ 'ShowBlocks' ] },
			{ name: 'snippets', items: [ 'CodeSnippet' ] }
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,bedita,onchange,webkit-span-fix,codesnippet,justify',
		language: BEDITA.currLang2,
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true,
		height:660
	},
	
	configSimple: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
			{ name: 'tools', items: [ 'ShowBlocks' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,onchange',
		language: BEDITA.currLang2,
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true
	},

	configMini: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,onchange',
		language: BEDITA.currLang2,
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
 		height:100,
 		toolbarLocation: 'bottom'
	},

	configNewsletterTemplate: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'ShowBlocks', 'BEditaContentBlock' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,bedita,onchange,beditacontentblock,webkit-span-fix,codesnippet,justify',
		language: BEDITA.currLang2,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
		height:660
	},

	configNewsletterMessage: {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'ShowBlocks' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,bedita,onchange,webkit-span-fix,codesnippet,justify',
		language: BEDITA.currLang2,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
		height:660
	}
}
	
$(window).load(function() {

	$( '.main textarea.mceFull' ).ckeditor(BEDITA.richtextConf.configFull);
	$( 'textarea.mce' ).ckeditor(BEDITA.richtextConf.configNormal);
	$( 'textarea.mceSimple' ).ckeditor(BEDITA.richtextConf.configSimple);
	$( '.richtext' ).ckeditor(BEDITA.richtextConf.configNormal);
	$( '.richtextSimple' ).ckeditor(BEDITA.richtextConf.configSimple);
	$( '.richtextMini' ).ckeditor(BEDITA.richtextConf.configMini);
	$('.richtextNewsletterTemplate').ckeditor(BEDITA.richtextConf.configNewsletterTemplate);
	$('.richtextNewsletterMessage').ckeditor(BEDITA.richtextConf.configNewsletterMessage);
		
	for (i in CKEDITOR.instances) {
		CKEDITOR.instances[i].on('key', onChangeHandler);
		CKEDITOR.instances[i].on('afterPaste', onChangeHandler);
	}
})
