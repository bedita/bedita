CKEDITOR.plugins.add('beditacontentblock', {

	init: function(editor) {

		editor.addCss('.beContentBlock {background: #ffffff url(' + this.path + 'images/content_block.gif) no-repeat right top; border-top: 1px dotted #cccccc; width: 100%; height: 12px; margin-top: 15px;}');
		var contentBlockPlaceHolder = '<!--bedita content block-->';
		var htmlContentBlockPlaceHolder = '<img class="beContentBlock" src="' + this.path + 'images/trans.gif"/>';
		var pathComment = new RegExp(contentBlockPlaceHolder, "g");
		var pathImage = /<img.*\sclass="beContentBlock".+?\/>/g;

		/* disable button if two bedita content blocks are present
		 * enable button if less then two content blocks are present
		 */
		var checkButtonState = function() {
			var data = editor.getData();
			var countContentBlockItems = null;
			if (editor.mode == "wysiwyg") {
				countContentBlockItems = data.match(pathImage);
				if (countContentBlockItems != null && countContentBlockItems.length == 2) {
					editor.getCommand('insertBEditaContentBlock').setState(CKEDITOR.TRISTATE_DISABLED);
				} else {
					editor.getCommand('insertBEditaContentBlock').setState(CKEDITOR.TRISTATE_OFF);
				}
			}
		};

		editor.addCommand('insertBEditaContentBlock', {
			exec: function(editor) {
				if (editor.mode == "source") {
					editor.insertHtml(contentBlockPlaceHolder);
				} else if (editor.mode == "wysiwyg") {
					editor.insertHtml(htmlContentBlockPlaceHolder);
				}
				checkButtonState();
			}
		});

		editor.ui.addButton('BEditaContentBlock', {
			label: 'Insert Content Block',
			command: 'insertBEditaContentBlock',
			icon: this.path + 'images/more.gif'
		});

		// on switch wysiwyg/source
		editor.on('mode', function(event) {
			var data = editor.getData();
			if (editor.mode == "source") {
				data = data.replace(pathImage, contentBlockPlaceHolder);
			} else if (editor.mode == "wysiwyg") {
				data = data.replace(pathComment, htmlContentBlockPlaceHolder);
			}
			editor.setData(data);
			checkButtonState();
		});

		editor.on('key', function(event) {
			checkButtonState();
		});

		editor.on('instanceReady', function(event) {
			checkButtonState();
		});

		/* trick to replace htmlContentBlockPlaceHolder with contentBlockPlaceHolder on submit
		 * No event is triggered on submit by ckeditor :(
		 * change it if in future version of ckeditor will be added a submit event
		 */
		$("#updateForm").submit(function() {
			// Get the editor data.
			var data = $('textarea.richtextNewsletterTemplate').val();
			data = data.replace(pathImage, contentBlockPlaceHolder);
			// Set the editor data.
			$( 'textarea.richtextNewsletterTemplate' ).val(data);
		});

	}
});