/**
 * Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

// This file contains style definitions that can be used by CKEditor plugins.
//
// The most common use for it is the "stylescombo" plugin, which shows a combo
// in the editor toolbar, containing all styles. Other plugins instead, like
// the div plugin, use a subset of the styles on their feature.
//
// If you don't have plugins that depend on this file, you can simply ignore it.
// Otherwise it is strongly recommended to customize this file to match your
// website requirements and design properly.

CKEDITOR.stylesSet.add( 'default', [

	/* Block Styles */

	{
		name: 'Dittico',
		element: 'P',
		attributes: { 'class': 'dittico' }
	},

	{
		name: 'Trittico',
		element: 'P',
		attributes: { 'class': 'trittico' }
	},
	
	{		
		name: 'Formula',
		element: 'P',
		attributes: { 'class': 'formula' }
	},
	
	{		
		name: 'Aside',
		element: 'aside'
	},

	/* Inline Styles */

	{ name: 'Definition',		element: 'dfn'},
	{ name: 'Marker',			element: 'mark'},

	{ name: 'Computer Code',	element: 'code' },
	{ name: 'Keyboard Phrase',	element: 'kbd' },
	{ name: 'Sample Text',		element: 'samp' },
	{ name: 'Variable',			element: 'var' },

	{ name: 'Deleted Text',		element: 'del' },
	{ name: 'Inserted Text',	element: 'ins' },

	{ name: 'Cited Work',		element: 'cite' },
	{ name: 'Inline Quotation',	element: 'q' },

	{
		name: 'Glossary term',
		element: 'dfn',
		attributes: { 'class': 'glossario' }
	},

	{ name: 'column break',	
		element: 'span', 
		attributes: { 'class': 'column-break' }
	},

	{ name: 'inline formula',	
		element: 'span', 
		attributes: { 'class': 'formula' }
	},

	{ name: 'inline aside',	
		element: 'span', 
		attributes: { 'class': 'aside' }
	}
	/* Object Styles */


]);

