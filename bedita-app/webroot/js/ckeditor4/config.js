/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	if (this.name.indexOf('body')!=-1 || this.name.indexOf('abstract')!=-1) {
		config.toolbar = [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			'/',
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },
			{ name: 'editAttributes', items: [ 'Attr' ] },
			{ name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
			{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar'/*, 'PageBreak'*/ ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			
			'/',
			{ name: 'styles', items: [ 'Format' , 'Styles'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		];
		config.resize_enabled = true;
	}
	
	if (this.name.indexOf('description')!=-1) {
		config.toolbar = [
			{ name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		];
		config.removePlugins = 'elementspath';
		config.resize_enabled = false;
	}
	
	config.language = 'it';
	config.startupOutlineBlocks = true;
	config.extraPlugins = 'codemirror,attributes,beButtons';
	config.browserContextMenuOnCtrl = true;
	config.codemirror = { theme: 'lesser-dark' }
	
};