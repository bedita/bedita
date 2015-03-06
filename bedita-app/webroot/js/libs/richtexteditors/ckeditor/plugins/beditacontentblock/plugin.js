CKEDITOR.plugins.add('beditacontentblock', {

	init: function(editor) {

		editor.addCommand('insertBEditaContentBlock', {
			exec: function(editor) {
				if (editor.mode == "source") {
					editor.insertHtml(contentBlockPlaceHolder);
				} else if (editor.mode == "wysiwyg") {
					editor.insertHtml(htmlContentBlockPlaceHolder);
				}
				checkButtonState();
			},
			async: true
		});

		editor.ui.addButton('BEditaContentBlock', {
			label: 'Insert Content Block',
			command: 'insertBEditaContentBlock',
			icon: this.path + 'images/more.gif'
		});

		var path = this.path;
		var contentBlockPlaceHolder = '<!--bedita content block-->';
		var htmlContentBlockPlaceHolder = '<img class="beContentBlock" src="' + path + 'images/trans.gif" />';
		var pathComment = new RegExp(contentBlockPlaceHolder, "g");
		var pathImage = /<img.? class="beContentBlock".*?\/>/g;
		var data = '';
		/* disable button if two bedita content blocks are present
		 * enable button if less then two content blocks are present
		 */
		var initTextArea = function() {
			data = editor.getData();
			data = data.replace(pathComment, htmlContentBlockPlaceHolder);
			editor.setData(data);
			checkButtonState();
		};

		var checkButtonState = function() {
			data = editor.getData();
			var i = data.match(pathImage) || [];
			if (i.length>=2 || editor.mode=="source") {
				editor.getCommand('insertBEditaContentBlock').setState(CKEDITOR.TRISTATE_DISABLED);
			} else {
				editor.getCommand('insertBEditaContentBlock').setState(CKEDITOR.TRISTATE_OFF);
			}
		};

		editor.on('instanceReady', function(event) {
			initTextArea();

			editor.on('beforeSetMode', function(event) {
				data = editor.getData();
				if (editor.mode == "wysiwyg") {
					data = data.replace(pathImage, contentBlockPlaceHolder);
					editor.setData(data);
				}
				return false;
			});

            $(editor.document.$).on('keyup', 'body', function() {
                checkButtonState();
            });

			editor.on('mode', function(event) {
				data = editor.getData();
				if (editor.mode == "wysiwyg") {
					data = data.replace(pathComment, htmlContentBlockPlaceHolder);
					editor.setData(data);
				} else {
					data = data.replace(pathImage, contentBlockPlaceHolder);
					editor.setData(data);
					$(editor.element.$).val(data);
				}
				checkButtonState();
			});
			CKEDITOR.addCss( '.beContentBlock {background: #ffffff url(' + path + 'images/content_block.gif) no-repeat right top; border-top: 1px dotted #cccccc; width: 100%; height: 12px; margin-top: 15px;}' );
		});

		/* trick to replace htmlContentBlockPlaceHolder with contentBlockPlaceHolder on submit
		 * No event is triggered on submit by ckeditor :(
		 * change it if in future version of ckeditor will be added a submit event
		 */
		$("#updateForm").submit(function() {
			// Get the editor data.
			data = editor.getData();
			data = data.replace(pathImage, contentBlockPlaceHolder);
			// Set the editor data.
			$( 'textarea.richtextNewsletterTemplate' ).val(data);
		});

	}
});
