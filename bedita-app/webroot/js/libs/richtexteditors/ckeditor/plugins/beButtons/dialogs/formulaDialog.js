CKEDITOR.dialog.add( 'formulaDialog', 
	function ( editor ) {
		// var currentDialog = this.getElement().$;
		var dialog = this;
		dialog.objectsChecked = [];
		dialog.displayInline = false;
		var response;
		return {
			title : 'Add formulas',
			minWidth : 660,
			minHeight : 200,
			contents :
			[
				{
					id : 'tab1',
					elements :
					[
						{
							type : 'html',
							id : 'formula-modal',
							html : '',
							style:'modal',
							'default': '',
							onShow: function() {
								var that = this;
								var currentID = BEDITA.id || 0;
								var destination = '/bedita-app/pages/showObjects/' + currentID + '/contains_formula/22';
								var input = that.getInputElement().$;
					            
					            $(input).empty().find('.loader').show();
					            $(input).load(destination, {}, function(response, status, xhr) {

					                $('.cke_dialog_contents_body').find('.loader').hide();
					                $('.inline-checkbox').show();
					                if (status == 'error') {
					                    $(input).html(response);
					                }
					                $(input).find('input[type="checkbox"].objectCheck').click(function() {
										var objectId = $(this).val();
										if ($(this).prop('checked')) {
											dialog.objectsChecked.add(objectId);
										} else {
											dialog.objectsChecked.remove(objectId);
										}
								    }); 
					            });
							}
						},
						{
							type: 'checkbox',
						    id: 'display',
						    label: 'Display inline',
						    'default': '',
						    className: 'inline-checkbox',
						    style: 'display:none;',
						    onClick: function() {
						        dialog.displayInline = this.getValue();
						    }
						}
					]
				}
			],
			buttons: [ CKEDITOR.dialog.cancelButton, CKEDITOR.dialog.okButton ],
			onLoad: function() {
				$('.cke_dialog_contents_body').append('<div class="loader"></div>');
			},
			onShow: function() {
				var currentDialog = this.getElement().$;
				$('.inline-checkbox').hide();
				
				$('.cke_dialog_contents_body').find('.loader').show();
				$(currentDialog).removeClass('cke_reset_all');
				$(currentDialog).addClass('formula-dialog');
			},
			onOk: function() {
				var that = this;
				var url, displayInline, ids = '';
				if (dialog.objectsChecked.list.length) {
					
					ids = dialog.objectsChecked.list.toString();
					url = '/bedita-app/formulas/svgMulti/' + ids + (dialog.displayInline ? '?inline' : '');
					$('#addButton').click();
					$.ajax({
						url: url,
						success: function (res) {
							if (res) {
								for (index in res) {
									var svg = CKEDITOR.dom.element.createFromHtml('<cke:object></cke:object>');
									var attributes = {
										'data-bedita-id' : index,
										'data-bedita-relation' : 'contains_formula'
									};
									svg.setAttributes(attributes);
									if (dialog.displayInline) {
										var params = CKEDITOR.dom.element.createFromHtml('<param name="inline" value="1" />');
										svg.append(params);
									}
									that.commitContent(svg);
									var newFakeImage = editor.createFakeElement( svg, 'cke_svg', 'svg', true );
									var encodedData = window.btoa(res[index]);
					 	 			newFakeImage.$.src = 'data:image/svg+xml;base64,' + encodedData;
					 	 			newFakeImage.$.alt = 'formula ' + index;
									editor.insertElement( newFakeImage );
								}
							}
						}
					});
				} 
			}
		};
});