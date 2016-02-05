CKEDITOR.plugins.add( 'beButtons', {
	icons: 'icons/formula',
    init: function( editor ) {
    	editor.ui.addButton( 'Dfn',
			{
				label : 'Dfn',
				command : 'definition'
			}
		);
		
		editor.ui.addButton( 'Formula',
			{
				label : 'f(x)',
				title: 'Add formulas',
				command : 'formula',
				icon: this.path + 'icons/formula.png',
			}
		);
		
		editor.ui.addButton( 'Glo',
			{
				label : 'Glo',
				command : 'glossary'
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
		
		editor.addCommand( 'formula', new CKEDITOR.dialogCommand( 'formulaDialog' ));
		
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
	    
	    CKEDITOR.dialog.add( 'formulaDialog', this.path + 'dialogs/formulaDialog.js' );
		
    },


    afterInit: function(editor) {
		var dataProcessor = editor.dataProcessor,
	        dataFilter = dataProcessor && dataProcessor.dataFilter;
	    if (dataFilter){
	        dataFilter.addRules({
	            elements : {
	                'cke:object' : function(element) {
						if (element && element['attributes'] && element['attributes']['data-bedita-relation'] == 'contains_formula') {
							var fakeElement = editor.createFakeParserElement( element, 'cke_svg', 'svg', true );
							if (element['attributes']['data-bedita-id']) {
								var formulaId = element['attributes']['data-bedita-id'];
								var url = BEDITA.base +'formulas/svg/' + formulaId;
								$.ajax({
									url: url,
									async: false,
									success: function (res) {
										if (res && res.svg) {
											var encodedData = window.btoa(res.svg);
											fakeElement.attributes['src'] = 'data:image/svg+xml;base64,' + encodedData;
										}
									}
								});
					    		return fakeElement;	
							}
						}	
	                }
	            }
	        }, 5);
	    }
    }
});

