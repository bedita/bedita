/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	var codemirrorTheme = 'monokai';

	config.language = BEDITA.currLang2,
	config.startupOutlineBlocks = true;
	config.extraPlugins = 'codemirror,justify,attributes,bedita,onchange,codesnippet';
	config.browserContextMenuOnCtrl = true;
	config.codemirror = { theme: codemirrorTheme };
	config.stylesSet = 'bedita:plugins/bedita/styles.js';
	config.contentsCss = CKEDITOR.basePath + 'plugins/bedita/contents.css';
	config.mathJaxClass = 'formula';

	if (!document.getElementById('codemirror-css-' + codemirrorTheme)) {
		var link = document.createElement('link');
			link.id = 'codemirror-css-' + codemirrorTheme;
			link.setAttribute('rel', 'stylesheet');
			link.setAttribute('type', 'text/css');
			link.href = CKEDITOR.basePath + 'plugins/codemirror/theme/' + codemirrorTheme + '.css';

		document.body.appendChild(link);
	}
};