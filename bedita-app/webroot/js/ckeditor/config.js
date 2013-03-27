/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = BEDITA.currLang2,
	config.startupOutlineBlocks = true;
	config.extraPlugins = 'codemirror,attributes,beButtons,onchange';
	config.browserContextMenuOnCtrl = true;
	config.codemirror = { theme: 'lesser-dark' }
};