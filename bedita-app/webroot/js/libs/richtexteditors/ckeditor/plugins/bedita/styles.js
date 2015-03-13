CKEDITOR.stylesSet.add( 'bedita', [

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

	{
		name: 'column break',	
		element: 'span', 
		attributes: { 'class': 'column-break' }
	},

	{
		name: 'inline formula',	
		element: 'span', 
		attributes: { 'class': 'formula' }
	}
	/* Object Styles */


]);