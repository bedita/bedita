var setPlaceholderCss = function(editor) {
	var d = editor.getData();
	var e = $(d);
	var jph = e.find('A.placeholder, A.plaref, A[target=modal]');
	var style = '<style id="placeholderCss">';
	if (editor.mode == "wysiwyg") {
		jph.each(function() {
			var href = $(this).attr('href').replace('#', '');
			var src = $('#relationType_attach .obj[data-benick="'+href+'"]').find('img').prop('src');
			style += ' A[href='+href+']:after{ background-image: url("'+src+'") } ';
		});
	}
	style += '</style>';
	if (editor.document) {
		$(editor.document.$).find('head').find('#placeholderCss').remove();
		$(editor.document.$).find('head').append(style);
	}
}

CKEDITOR.dialog.add('placeholderDialog', function(editor) {
	return {
		title : 'Placeholder Properties',
		minWidth : 400,
		minHeight : 200,
		contents :
		[
			{
				id : 'general',
				label : 'Settings',
				elements :
				[
				 	{
			 	        type: 'select',
			 	        id: 'type',
			 	        label: 'Type',
			 	        'default': 'placeholder',
			 	        items: [
			 	        	['placeholder', 'inline'],
			 	        	['anchor', 'anchor']
			 	        ]
			 	    },
			 	    {
			 	        type: 'select',
			 	        id: 'target',
			 	        label: 'Actions',
			 	        'default': 'href',
			 	        items: ['href', 'scroll', 'placeref', 'insert']
			 	    }
				]
			}
		]
	};
});

CKEDITOR.plugins.add( 'bedita', {
    init: function( editor ) {

    	editor.on('change',function() {
    		if (editor.mode == "wysiwyg") {
    			setPlaceholderCss(this);
    		}
    	});
    	
    	editor.on('mode', function(event) {
    		if (editor.mode == "wysiwyg") {
    			setPlaceholderCss(this);
    		}
    	});
    	
    	editor.on('instanceReady', function() {
    		if (editor.mode == "wysiwyg") {
	    		setPlaceholderCss(editor);
	    	}
	    });

    	editor.ui.addButton( 'Dfn',
			{
				label : 'Dfn',
				command : 'definition'
			}
		);
		
		editor.ui.addButton( 'x\y',
			{
				label : 'x/y',
				command : 'formula'
			}
		);
		
		editor.ui.addButton( 'Glo',
			{
				label : 'Glo',
				command : 'glossary'
			}
		);

		editor.ui.addButton( 'MediaAttach',
			{
				label : 'Inser Media',
				command : 'attachMedia',
				icon: this.path + 'icons/media.png',
			}
		);
		
		editor.addCommand( 'definition',
			{
			    exec : function( editor )
			    {
			    	var selection = editor.getSelection().getNative();
					var index = selection.anchorOffset;
					var end = selection.focusOffset;
					var node = selection.anchorNode.parentNode;
					var otext = selection.focusNode.wholeText;
					var text = selection.focusNode.nodeValue;
					
					var selectText = text.substr(index,end);
					
					var newText = text.substr(0,index) + '|voglioCheSiaUnaDefinizione|' + selectText + '|voglioCheSiaUnaDefinizione|' + text.substr(end);
					selection.focusNode.textContent = newText;
					var html = node.innerHTML;
					html = html.replace('|voglioCheSiaUnaDefinizione|' + selectText + '|voglioCheSiaUnaDefinizione|','<dfn>'+selectText+'</dfn>');
					node.innerHTML = html;
			    },
			    async : true
			}
		);
		
		editor.addCommand( 'formula',
			{
			    exec : function( editor )
			    {
			    	var selection = editor.getSelection().getNative();
					var index = selection.anchorOffset;
					var end = selection.focusOffset;
					var node = selection.anchorNode.parentNode;
					var otext = selection.focusNode.wholeText;
					var text = selection.focusNode.nodeValue;
					
					var selectText = text.substr(index,end);
					
					var newText = text.substr(0,index) + '|voglioCheSiaUnaFormula|' + selectText + '|voglioCheSiaUnaFormula|' + text.substr(end);
					selection.focusNode.textContent = newText;
					var html = node.innerHTML;
					html = html.replace('|voglioCheSiaUnaFormula|' + selectText + '|voglioCheSiaUnaFormula|','<span class="formula">'+selectText+'</span>');
					node.innerHTML = html;
			    },
			    async : true
			}
		);
		
		editor.addCommand( 'glossary',
			{
			    exec : function( editor )
			    {
			    	var selection = editor.getSelection().getNative();
					var index = selection.anchorOffset;
					var end = selection.focusOffset;
					var node = selection.anchorNode.parentNode;
					var otext = selection.focusNode.wholeText;
					var text = selection.focusNode.nodeValue;
					
					var selectText = text.substr(index,end);
					
					var newText = text.substr(0,index) + '|voglioCheSiaUnGlossario|' + selectText + '|voglioCheSiaUnGlossario|' + text.substr(end);
					selection.focusNode.textContent = newText;
					var html = node.innerHTML;
					html = html.replace('|voglioCheSiaUnGlossario|' + selectText + '|voglioCheSiaUnGlossario|','<dfn class="glossario">'+selectText+'</dfn>');
					node.innerHTML = html;
			    },
			    async : true
			}
		);

		editor.addCommand('addPlaceholder', {

			exec: function(editor, options) {

				editor.execCommand('insertPlaceholder', options);
				
				var dialog = new CKEDITOR.dialogCommand('link');
				dialog.exec(editor);

			}

		});

		editor.addCommand('insertPlaceholder', {

			exec: function(editor, options) {
				var element = document.createElement('a');
				element.setAttribute('href', options.id);
				var selection = editor.getSelection();
				var emptyText = '&#8203;';
				var textToReplace = selection == null ? emptyText : selection.getSelectedText();
				if (textToReplace == '') {
					textToReplace = emptyText;
				}
				element.innerHTML = textToReplace;
				editor.insertHtml(element.outerHTML);
			}

		});

		var unbind = function() {
			$(document).unbind('relation_attach:added relation_attach:refused operation:cancel');
		}

		var types = BEDITA.richtextConf.attachMedia.types || [['placeholder', 'placeholder'],['anchor', 'anchor']];

		CKEDITOR.on('dialogDefinition', function(ev) {
			if (ev.data.name == 'link') {
				var dialog = ev.data.definition;
				dialog.addContents({
					id : 'placeholder',
					label : 'Placeholder',
					elements :
					[
					 	{
				 	        type: 'select',
				 	        id: 'placeholderType',
				 	        label: 'Placeholder Type',
				 	        'default': 'anchor',
				 	        items: types,
				 	        commit: function(data) {
				 	        	var type = this.getValue();
				 	        	if (data.advanced && data.advanced.advCSSClasses !== undefined) {
				 	        		var div = $('<div>');
    			 	        		div.addClass(data.advanced.advCSSClasses || '');
    			 	        		for (var i = 0; i < types.length; i++) {
    			 	        			var t = types[i][0];
    			 	        			if (t == type) {
    			 	        				div.addClass(t);
    			 	        			} else {
    			 	        				div.removeClass(t);
    			 	        			}
    			 	        		}
    				 	        	data.advanced.advCSSClasses = div.attr('class');
					 	        }
				 	        },
                            setup: function(data) {
                            	if (data.advanced && data.advanced.advCSSClasses) {
                            		var cl = data.advanced.advCSSClasses.split(' ');
                            		for (var i = 0; i < types.length; i++) {
                            			var t = types[i][0];
                            			if (cl.indexOf(t) != -1) {
                            				this.setValue(t);
                            			}
                            		}
                            	}
                            }
				 	    }
					]
				});
			}
		});

		editor.addCommand('attachMedia', {
			exec: function(editor) {
				if (BEDITA.richtextConf && BEDITA.richtextConf.attachMedia) {
					new BEmodal({
						title: BEDITA.richtextConf.attachMedia.title,
						destination: BEDITA.richtextConf.attachMedia.page
					});

					unbind();

					$(document)
						.bind('operation:cancel', function() {
							unbind();
						})
						.bind('relation_attach:added relation_attach:refused', function(ev, args) {
							var id = $(args).attr('data-benick');
							editor.execCommand('addPlaceholder', {
								id: id
							});

							unbind();
						});
				}
			}
		})
		
    }
});