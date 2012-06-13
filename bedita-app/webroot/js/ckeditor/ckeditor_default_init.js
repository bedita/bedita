
$(function()
{

	var configFull = {
			language: BEDITA.currLang2,
		    uiColor: '#dedede',
			extraPlugins: 'stylesheetparser',
			contentsCss: BEDITA.webroot + 'css/htmleditor.css', 
			toolbar: 'BEToolbarFull',
			toolbar_BEToolbarFull: [
				{ name: 'document', items : [ 'Source','-','Save' ] },
				{ name: 'clipboard', items : [ 'PasteText','PasteFromWord','-','Undo','Redo' ] },
				{ name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
				{ name: 'basicstyles', items : [ 'Bold','Italic','Underline',
				                                 'Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
				'/',
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-',
				                               'Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight',
				                               'JustifyBlock' ] },
				{ name: 'links', items : [ 'Link','Unlink' ] },
				{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar' ] },
				{ name: 'colors', items : [ 'TextColor','BGColor' ] },
				{ name: 'styles', items : [ 'Styles','Format' ] }	]
	};

	var configSimple = {
			language: BEDITA.currLang2,
		    uiColor: '#dedede',
			extraPlugins: 'stylesheetparser',
			contentsCss: BEDITA.webroot + 'css/htmleditor.css', 
			toolbar: 'BEToolbarSimple',
			toolbar_BEToolbarSimple: [
				{ name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
				{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
				{ name: 'links', items : [ 'Link','Unlink' ] }
			]
		};

	// mce compatibility
	$( '.mce' ).ckeditor(configFull);
	$( '.mceSimple' ).ckeditor(configSimple);

	$( '.richtext' ).ckeditor(configFull);
	$( '.richtextSimple' ).ckeditor(configSimple);
});
