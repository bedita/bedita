
CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	config.language = 'it';
    config.uiColor = '#dedede';
	config.extraPlugins = 'stylesheetparser';
	config.contentsCss = '/css/htmleditor.css';
	
	config.toolbar = 'BEToolbar';
 
	config.toolbar_BEToolbar =
	[
	{ name: 'document', items : [ 'Source','-','Save' ] },
	{ name: 'clipboard', items : [ 'PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
	'/',
	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
	{ name: 'links', items : [ 'Link','Unlink' ] },
	{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar' ] },
	//{ name: 'colors', items : [ 'TextColor','BGColor' ] },
	{ name: 'styles', items : [ 'Styles','Format' ] }	];

	//config.stylesSet = [];
};
