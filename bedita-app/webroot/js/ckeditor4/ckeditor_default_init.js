$(window).load(function() {
	$( '.main textarea.mceFull' ).ckeditor();
	var configFull = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find'/*, 'Replace'*/ ] },
			{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ /*'Paste', */'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'tools', items: [/* 'Maximize',*/ 'ShowBlocks' ] },
		],
		resize_enabled: true,
		extraPlugins: 'codemirror,attributes,beButtons,onchange',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' }
	};
	
	var configSimple = {
		toolbar: [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
			{ name: 'tools', items: [/* 'Maximize', */'ShowBlocks' ] },
		],
		resize_enabled: true,
		extraPlugins: 'codemirror,onchange',
		language: BEDITA.currLang2,
		codemirror: { theme: 'lesser-dark' }
	}
	
	
	$( 'textarea.mce' ).ckeditor(configFull);
	$( 'textarea.mceSimple' ).ckeditor(configSimple);
	$( '.richtext' ).ckeditor(configFull);
	$( '.richtextSimple' ).ckeditor(configSimple);
		
	for (i in CKEDITOR.instances) {
		CKEDITOR.instances[i].on('change', onChangeHandler);
	}
})
