var FlatLanderEditor;

$(window).load(function() {
    if (window['Flatlander']!=undefined) {
        FlatLanderEditor = new Flatlander({
            el: $('#multimediaitem img')[0]
        });

        var newArea;

        FlatLanderEditor.FlatlanderEditorInstance.appendInput({
            type: 'textarea',
            name: 'title',
            editable: false,
        });

        FlatLanderEditor.FlatlanderEditorInstance.appendInput({
            type: 'textarea',
            name: 'link',
            editable: false,
        });

        function hideMediamaps() {
            $('#toggleMediamap').text($('#toggleMediamap').data('show'));
            FlatLanderEditor.$editor.hide();
            FlatLanderEditor.$workspace.hide();
        }

        function showMediamaps() {
            $('#toggleMediamap').text($('#toggleMediamap').data('hide'));
            FlatLanderEditor.$workspace.show();
        }

        $('#toggleMediamap').on('click', function(ev) {
            ev.preventDefault();
            ev.stopPropagation();
            var visible = FlatLanderEditor.$workspace.is(':visible');
            if (visible) {
                hideMediamaps();
            } else {
                showMediamaps();
            }

            return false;
        });

        if (BEDITA && BEDITA.relations && BEDITA.relations.mediamap && BEDITA.relations.mediamap.params) {
            for (var k in BEDITA.relations.mediamap.params) {
                var r = BEDITA.relations.mediamap.params[k];
                if (r instanceof Array) {
                    FlatLanderEditor.FlatlanderEditorInstance.appendInput({
                        type: 'select',
                        name: k,
                        options: r
                    });
                } else {
                    FlatLanderEditor.FlatlanderEditorInstance.appendInput({
                        type: 'textarea',
                        name: r
                    });
                }
            }
        }

        FlatLanderEditor.FlatlanderEditorInstance.$el.find('[data-name="background-upload"]').bind('change', function() {
            if (this.files.length==0) {
                $('.activeArea').attr('data-backgroundimage','').css('background-image','none');
            } else {
                var img = this.files[0];
                var reader = new FileReader();
                reader.onload = function(f) {
                    var base64Image = f.target.result;
                    FlatLanderEditor.FlatlanderEditorInstance.currentArea.addBackground(base64Image);
                };
                reader.readAsDataURL(img);
            }
        });

        var onRelationInputChange = function() {
            var el = $(this);
            var areaid = el.closest('.obj').attr('data-flatlanderarea-id');
            var val = el.val();
            var label = el.attr('name').split('[').pop().replace(']','');
            FlatLanderEditor.areas[areaid].set(label, val, true);
        };

        var onRelationInputClick = function() {
            var obj = $(this).closest('.obj');
            var areaid = obj.attr('data-flatlanderarea-id');
            var area = FlatLanderEditor.areas[areaid];
            area.$el.trigger('mousedown');
        };

        var onAreaChange = function(area) {
            var a = area.toJSON();
            var tr = $('[data-flatlanderarea-id="'+a.id+'"]');
            for (var k in a) {
                if (k!='id') {
                    var f = tr.find('input, select').filter(function() {
                        var name = this.getAttribute('name') || '';
                        return name.indexOf('['+k+']')!=-1;
                    });
                    f.val(a[k]).trigger('change');
                }
            }

            var trs = $('#relationType_mediamap .obj');
            var list = tr.parent();
            var reorder = [];
            for (var k in FlatLanderEditor.areas) {
                var a = FlatLanderEditor.areas[k];
                reorder[a.get('priority')] = a;
            }
            for (var i=1; i<=reorder.length; i++) {
                var a = reorder[i];
                if (a) {
                    var tr = trs.filter('[data-flatlanderarea-id="'+a.get('id')+'"]');
                    list.append(tr);
                }
            }
        };

        var onAreaDelete = function(area) {
            var a = area.toJSON();
            var tr = $('[data-flatlanderarea-id="'+a.id+'"]');
            if (tr.length>0) tr.find('[name="remove"]').click();
        };

        $('#relationType_mediamap .obj').each(function() {
            var $el = $(this);
            
            var div = $('<div>');
            div.addClass(FlatLanderEditor.classList.area);
            FlatLanderEditor.$workspace.append(div);
            var areaObj = new FlatlanderArea(div, FlatLanderEditor);

            areaObj.set({
                priority: $el.find('.priority').val(),
            });

            $el.find('.moredata input, .moredata textarea, .moredata select').each(function() {
                if ($(this).attr('name')) {
                    var val = $(this).val();
                    var label = $(this).attr('name').split('[').pop().replace(']','');
                    areaObj.set(label, val);
                }
            });

            $el.find('input').bind('change.fl keyup.fl', onRelationInputChange);

            areaObj.set('title', $el.find('.assoc_obj_title h4').text());
            areaObj.set('link', $el.attr('data-beid'));
            areaObj.set('background', $el.find('.rel_uri').val() || 'none');

            FlatLanderEditor.FlatlanderEditorInstance.appendArea(areaObj);
            FlatLanderEditor.trigger('areacreated', areaObj);
            
            areaObj.bind('change', function() {
                onAreaChange(areaObj);
            });
            areaObj.bind('deleted', function() {
                onAreaDelete(areaObj);
            });
            
            $el.attr('data-flatlanderarea-id', areaObj.get('id'));
            $el.find('[name="remove"]').click(function() {
                var id = $el.attr('data-flatlanderarea-id');
                FlatLanderEditor.areas[id].delete();
            })
        }).find('.assoc_obj_title').click(onRelationInputClick);

        $(document).on('click.flatlander', function(ev) {
            if (!$(ev.target).is('.flatlanderWrapper, .flatlanderWrapper *')) {
                FlatLanderEditor.FlatlanderEditorInstance.$el.slideUp('fast');
            } else {
                if ($(ev.target).is('.flatlanderArea, .flatlanderArea *')) {
                    FlatLanderEditor.FlatlanderEditorInstance.$el.slideDown('fast');
                }
            }
        });

        $(document).bind('relation_mediamap:added', function(ev, args) {
            var area = newArea;
            if (area == null) {
                var div = $('<div>');
                div.addClass(FlatLanderEditor.classList.area);
                FlatLanderEditor.$workspace.append(div);
                area = new FlatlanderArea(div, FlatLanderEditor);

                FlatLanderEditor.FlatlanderEditorInstance.appendArea(area);
            }

            $(args).attr('data-flatlanderarea-id', area.get('id')).find('.relparams input').bind('change.fl keyup.fl', onRelationInputChange);
            area.set('title', $(args).find('.assoc_obj_title h4').text());
            area.set('link', $(args).attr('data-beid'));
            area.set('background', $(args).find('.rel_uri').val() || 'none');

            area.bind('change', function() {
                onAreaChange(area);
            });

            area.trigger('change');

            $(args).find('.assoc_obj_title').click(onRelationInputClick);

            $(args).find('[name="remove"]').click(function() {
                var id = $(this).closest('.obj').attr('data-flatlanderarea-id');
                FlatLanderEditor.areas[id].delete();
            })

            newArea = null;
            $(document).unbind('operation:cancel');
        });

        FlatLanderEditor.bind('areacreated', function(area) {
            $('[rel="relationType_mediamap"]').click();

            var id = area.get('id');
            area.bind('change', function() {
                onAreaChange(area);
            });
            area.bind('deleted', function() {
                onAreaDelete(area);
            });

            newArea = area;

            $(document).bind('operation:cancel', function() {
                area.delete();
                $(document).unbind('operation:cancel');
            });



            $('#relationType_mediamap .modalbutton').BEmodal();
        });

        if ($('#toggleMediamap').data('start') == 'hidden') {
            hideMediamaps();
        } else {
            showMediamaps();
        }
        FlatLanderEditor.$editor.hide();
    }
});