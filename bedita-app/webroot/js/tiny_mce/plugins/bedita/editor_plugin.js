/**
 * Bedita plugin.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.Bedita', {
		init : function(ed, url) {
			var t = this, tbId = ed.getParam('wordpress_adv_toolbar', 'toolbar2');
			var beContentHTML = '<img src="' + url + '/img/trans.gif" class="mceBeContentBlock mceItemNoResize" title="Content Block" />';

			if ( tinymce.util.Cookie.get('kitchenSink') == '1' )
				ed.settings.wordpress_adv_hidden = 0;

			// Hides the specified toolbar and resizes the iframe
			// Register commands
			ed.addCommand('BE_ContentBlock', function() {
				ed.execCommand('mceInsertContent', 0, beContentHTML);
			});


			// Register buttons
			ed.addButton('be_content', {
				title : 'Insert Content Block',
				image : url + '/img/more.gif',
				cmd : 'BE_ContentBlock'
			});

			// Add listeners to handle more break
			t._handleBeContentBlock(ed, url);

			// Add custom shortcuts
			ed.addShortcut('alt+shift+t', "Insert Content Block", 'BE_ContentBlock');
		},

		getInfo : function() {
			return {
				longname : 'Bedita Plugin',
				author : 'ChannelWeb',
				authorurl : 'http://channelweb.it',
				infourl : 'http://channelweb.it',
				version : '1.0'
			};
		},

		// Internal functions
		_handleBeContentBlock : function(ed, url) {
			var beContentHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mceBeContentBlock mceItemNoResize" title="Content Block" />';

			// Load plugin specific CSS into editor
			ed.onInit.add(function() {
				ed.dom.loadCSS(url + '/css/content.css');
			});
			
			// Replace bedita content block with images
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<!--bedita content block(.*?)-->/g, beContentHTML);
			});

			// Replace images with bedita content block
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mceBeContentBlock') !== -1) {
							var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--bedita content block'+moretext+'-->';
						}
						
						return im;
					});
			});

			// Set active buttons if user selected bedita content block
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('be_content', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceBeContentBlock'));
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bedita', tinymce.plugins.Bedita);
})();
