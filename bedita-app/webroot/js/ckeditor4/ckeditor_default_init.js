$(window).load(function() {
	$( '.main textarea.mceFull' ).ckeditor();
	var configFull = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		],
		resize_enabled: true,
		extraPlugins: 'onchange',
		language: BEDITA.currLang2
	};
	
	var configSimple = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		],
		removePlugins: 'elementspath',
		resize_enabled: true,
		extraPlugins: 'onchange',
		language: BEDITA.currLang2
	}
	
	
	$( 'textarea.mce' ).ckeditor(configFull);
	$( 'textarea.mceSimple' ).ckeditor(configSimple);
	$( '.richtext' ).ckeditor(configFull);
	$( '.richtextSimple' ).ckeditor(configSimple);
		
	for (i in CKEDITOR.instances) {
		CKEDITOR.instances[i].on('change', onChangeHandler);
	}
})
