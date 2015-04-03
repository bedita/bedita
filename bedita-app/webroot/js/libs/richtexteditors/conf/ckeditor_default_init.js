$(window).load(function() {
	var configFull = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ /*'Maximize', */'ShowBlocks' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' },
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true,
		height:660
	};
	
	var configNormal = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			/*{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },*/
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find'/*, 'Replace'*/ ] },
			{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ /*'Paste', */'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'tools', items: [/* 'Maximize',*/ 'ShowBlocks' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' },
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true,
		height:660
	};
	
	var configSimple = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
			{ name: 'tools', items: [/* 'Maximize', */'ShowBlocks' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,onchange',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' },
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: true,
	};

	var configMini = {
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
		codemirror: { theme: 'lesser-dark' },
		entities:false,
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
 		height:100,
 		toolbarLocation: 'bottom'
	};

	var configNewsletterTemplate = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ /*'Maximize', */'ShowBlocks', 'BEditaContentBlock' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,beButtons,onchange,beditacontentblock,webkit-span-fix',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' },
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
		height:660
	};

	var configNewsletterMessage = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		allowedContent: true,
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' },
		fillEmptyBlocks:false,
 		forcePasteAsPlainText:true,
 		startupOutlineBlocks: false,
		height:660
	};
	
	$( '.main textarea.mceFull' ).ckeditor(configFull);
	$( 'textarea.mce' ).ckeditor(configNormal);
	$( 'textarea.mceSimple' ).ckeditor(configSimple);
	$( '.richtext' ).ckeditor(configNormal);
	$( '.richtextSimple' ).ckeditor(configSimple);
	$( '.richtextMini' ).ckeditor(configMini);
	$('.richtextNewsletterTemplate').ckeditor(configNewsletterTemplate);
	$('.richtextNewsletterMessage').ckeditor(configNewsletterMessage);
		
	for (i in CKEDITOR.instances) {
		CKEDITOR.instances[i].on('key', onChangeHandler);
		CKEDITOR.instances[i].on('afterPaste', onChangeHandler);
	}
})
