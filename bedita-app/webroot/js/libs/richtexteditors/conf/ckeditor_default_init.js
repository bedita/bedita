if (!BEDITA) {
	var BEDITA = {};
}

BEDITA.richtexteditor = {
	ckeditor: {
		configFull: {
			toolbar: [
				{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
				{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
				'/',
				{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
				{ name: 'editAttributes', items: [ 'Attr' ] },
				{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
				{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
				{ name: 'tools', items: [ /*'Maximize', */'ShowBlocks', 'AutoCorrect' ] },
				'/',
				{ name: 'styles', items: [ 'Format' , 'Styles'] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			],
			allowedContent: true,
			resize_enabled: true,
			autocorrect_enabled: true,
			extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix,autocorrect',
			language: BEDITA.currLang2,
			codemirror: { theme: 'lesser-dark' },
			fillEmptyBlocks:false,
			forcePasteAsPlainText:true,
			startupOutlineBlocks: true,
			height:660
		},

		configNormal: {
			toolbar: [
				{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
				{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
				{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
				'/',
				/*{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },*/
				{ name: 'editAttributes', items: [ 'Attr' ] },
				{ name: 'editing', groups: [ 'find'], items: [ 'Find'/*, 'Replace'*/ ] },
				{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
				{ name: 'styles', items: [ 'Format' , 'Styles'] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ /*'Paste', */'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
				{ name: 'tools', items: [/* 'Maximize',*/ 'ShowBlocks', 'AutoCorrect' ] },
			],
			allowedContent: true,
			resize_enabled: true,
			autocorrect_enabled: true,
			extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix,autocorrect',
			language: BEDITA.currLang2,
			codemirror: { theme: 'lesser-dark' },
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
				{ name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
				{ name: 'tools', items: [/* 'Maximize', */'ShowBlocks', 'AutoCorrect' ] },
			],
			allowedContent: true,
			resize_enabled: true,
			autocorrect_enabled: true,
			extraPlugins: 'codemirror,onchange,autocorrect',
			language: BEDITA.currLang2,
			codemirror: { theme: 'lesser-dark' },
			entities:false,
			fillEmptyBlocks:false,
			forcePasteAsPlainText:true,
			startupOutlineBlocks: true,
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
			codemirror: { theme: 'lesser-dark' },
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
				{ name: 'tools', items: [ /*'Maximize', */'ShowBlocks', 'BEditaContentBlock', 'AutoCorrect' ] },
				'/',
				{ name: 'styles', items: [ 'Format' , 'Styles'] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			],
			allowedContent: true,
			resize_enabled: true,
			autocorrect_enabled: true,
			extraPlugins: 'codemirror,attributes,beButtons,onchange,beditacontentblock,webkit-span-fix,autocorrect',
			language: BEDITA.currLang2,
			codemirror: { theme: 'lesser-dark' },
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
				{ name: 'tools', items: [ 'Maximize', 'ShowBlocks', 'AutoCorrect' ] },
				'/',
				{ name: 'styles', items: [ 'Format' , 'Styles'] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			],
			allowedContent: true,
			resize_enabled: true,
			autocorrect_enabled: true,
			extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix,autocorrect',
			language: BEDITA.currLang2,
			codemirror: { theme: 'lesser-dark' },
			fillEmptyBlocks:false,
			forcePasteAsPlainText:true,
			startupOutlineBlocks: false,
			height:660
		}
	}
};



$(window).load(function() {
	$( '.main textarea.mceFull' ).ckeditor(BEDITA.richtexteditor.ckeditor.configFull);
	$( 'textarea.mce' ).ckeditor(BEDITA.richtexteditor.ckeditor.configNormal);
	$( 'textarea.mceSimple' ).ckeditor(BEDITA.richtexteditor.ckeditor.configSimple);
	$( '.richtext' ).ckeditor(BEDITA.richtexteditor.ckeditor.configNormal);
	$( '.richtextSimple' ).ckeditor(BEDITA.richtexteditor.ckeditor.configSimple);
	$( '.richtextMini' ).ckeditor(BEDITA.richtexteditor.ckeditor.configMini);
	$('.richtextNewsletterTemplate').ckeditor(BEDITA.richtexteditor.ckeditor.configNewsletterTemplate);
	$('.richtextNewsletterMessage').ckeditor(BEDITA.richtexteditor.ckeditor.configNewsletterMessage);
		
	if (BEDITA.webroot) {
		if(typeof CKEDITOR.config.contentsCss === 'string') {
			CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss, BEDITA.webroot + 'js/libs/richtexteditors/conf/ckeditor_default_init.css'];
		} else {
			CKEDITOR.config.contentsCss.push( BEDITA.webroot + 'js/libs/richtexteditors/conf/ckeditor_default_init.css' );
		}
	}

	for (i in CKEDITOR.instances) {
		CKEDITOR.instances[i].on('key', onChangeHandler);
		CKEDITOR.instances[i].on('afterPaste', onChangeHandler);
	}
})
