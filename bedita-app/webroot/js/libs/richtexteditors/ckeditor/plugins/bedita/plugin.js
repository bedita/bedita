CKEDITOR.plugins.add( 'bedita', {
    init: function( editor ) {
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
		
    }
});