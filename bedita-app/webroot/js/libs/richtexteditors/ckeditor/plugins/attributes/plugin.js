CKEDITOR.plugins.add( 'attributes', {
    init: function( editor ) {
    	editor.ui.addButton( 'Attr',
			{
				label : 'Attr',
				command : 'editAttr'
			}
		);
		
		editor.addCommand( 'editAttr', new CKEDITOR.dialogCommand( 'attrDialog' ) );
    }
});

CKEDITOR.dialog.add( 'attrDialog', 
	function ( editor ) {
		return {
			title : 'Insert/Edit attributes',
			minWidth : 400,
			minHeight : 200,
	 
			contents :
			[
				{
					id : 'tab1',
					label : 'Attributes',
					elements :
					[
						{
							type : 'text',
							id : 'ID',
							label : 'Id'
						},
						{
							type : 'text',
							id : 'class',
							label : 'Class',
						},
						{
							type : 'text',
							id : 'title',
							label : 'Title',
						},
						{
							type : 'text',
							id : 'style',
							label : 'Style',
						},
						{
							type : 'select',
							id : 'text-dir',
							label : 'Text Direction',
							style:'width:100%',
							items: [ ["Left to Right"],['Right to Left'] ]
						},
						{
							type : 'text',
							id : 'lang',
							label : 'Language',
						},
						{
							type : 'text',
							id : 'tabindex',
							label : 'Tab-Index',
						},
						{
							type : 'text',
							id : 'key',
							label : 'Access Key',
						},
					]
				}
			],
			
			onLoad: function() {
				var dialog = this;
				var selection = editor.getSelection().getNative();
				var node = selection.anchorNode.parentNode;
				
				if (node.id) dialog.setValueOf( 'tab1' , 'ID' , node.id );
				if (node.getAttribute('class')) dialog.setValueOf( 'tab1' , 'class' , node.getAttribute('class') );
				if (node.getAttribute('style')) dialog.setValueOf( 'tab1' , 'style' , node.getAttribute('style') );
				if (node.getAttribute('title')) dialog.setValueOf( 'tab1' , 'title' , node.getAttribute('title') );
				var dir = node.getAttribute('dir');
				if (dir == 'ltr') dialog.setValueOf( 'tab1' , 'text-dir' , 'Left to Right' );
				if (dir == 'rtl') dialog.setValueOf( 'tab1' , 'text-dir' , 'Right to Left' );
				if (node.getAttribute('lang')) dialog.setValueOf( 'tab1' , 'lang' , node.getAttribute('lang') );
				if (node.getAttribute('tabindex')) dialog.setValueOf( 'tab1' , 'tabindex' , node.getAttribute('tabindex') );
				if (node.getAttribute('accesskey')) dialog.setValueOf( 'tab1' , 'key' , node.getAttribute('accesskey') );
			},
			
			onOk: function() {
				var dialog = this;
				var selection = editor.getSelection().getNative();
				var node = selection.anchorNode.parentNode;
				
				var val = dialog.getValueOf( 'tab1', 'ID' );
				if (val!="") node.id = val;
				var val = dialog.getValueOf( 'tab1', 'class' );
				if (val!="") node.class = val;
				var val = dialog.getValueOf( 'tab1', 'style' );
				if (val!="") node.setAttribute('style',val);
				var val = dialog.getValueOf( 'tab1', 'title' );
				if (val!="") node.setAttribute('title',val);
				var val = dialog.getValueOf( 'tab1', 'class' );
				if (val!="") node.setAttribute('class',val);
				var val = dialog.getValueOf( 'tab1', 'text-dir' );
				if (val=='Left to Right') node.setAttribute('dir','ltr');
				if (val=='Right to Left') node.setAttribute('dir','rtl');
				var val = dialog.getValueOf( 'tab1', 'lang' );
				if (val!="") node.setAttribute('lang',val);
				var val = dialog.getValueOf( 'tab1', 'tabindex' );
				if (val!="") node.setAttribute('tabindex',val);
				var val = dialog.getValueOf( 'tab1', 'key' );
				if (val!="") node.setAttribute('accesskey',val);				
			}
		};
		
		
} 
);