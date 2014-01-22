var FlatLanderEditor;

$(window).load(function() {
    FlatLanderEditor = new Flatlander({
        el: $('#advanced-multimedia-editor img')[0]
    });

    var editorInputs = {
        number: '<textarea rows="1" type="text" data-name="number" /></textarea>',
        title: '<textarea data-name="title" readonly rows="1"></textarea>',
        link: '<textarea data-name="link" readonly style="cursor: pointer" rows="1"></textarea>',
        style: '<select data-name="style"><option>none</option><option>bordered</option><option>fill</option><option>pointer</option></select>',
        behaviour: '<select data-name="behaviour"><option>skin</option><option>popup</option><option>popup & zoom</option><option>modal</option></select>',
        direction: '<select data-name="direction"><option value="auto">auto</option><option value="n">North</option><option value="w">West</option><option value="e">East</option><option value="s">South</option><option value="nw">North - West</option><option value="ne">North - East</option><option value="sw">South - West</option><option value="se">South - East</option></select>',
    };

    var newArea;

    for (var k in editorInputs) {
        var o = editorInputs[k];
        if (k=="link") {
            FlatLanderEditor.FlatlanderEditorInstance.appendInput({
                el: $(o)[0],
                onClick: function(el) {
                    var id = $(el).val();
                    window.open( window.location.origin+BEDITA.base+'view/'+id, '_blank');
                }
            });
        } else {
            FlatLanderEditor.FlatlanderEditorInstance.appendInput({
                el: $(o)[0],
            });
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
                var f = tr.find('input').filter(function() {
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
            id: 'area_'+$el.find('.priority').val(),
            priority: $el.find('.priority').val(),
        });
        $el.find('.relparams table input').each(function() {
            var val = $(this).val();
            var label = $(this).attr('name').split('[').pop().replace(']','');
            areaObj.set(label, val);
        });

        $el.find('input').bind('change.fl keyup.fl', onRelationInputChange);

        areaObj.set('title', $el.find('.assoc_obj_title').html());
        areaObj.set('link', $el.attr('data-beid'));
        areaObj.set('background', $el.find('.rel_uri').val());

        FlatLanderEditor.FlatlanderEditorInstance.appendArea(areaObj);
        FlatLanderEditor.areas[areaObj.get('id')] = areaObj;
        FlatLanderEditor.trigger('areacreated', areaObj);
        FlatLanderEditor.numberOfElements++;
        
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

    $(document).bind('relation:added', function(ev, args) {
        var area = newArea;
        console.log(area);
        $(args).attr('data-flatlanderarea-id', area.get('id')).find('.relparams input').bind('change.fl keyup.fl', onRelationInputChange);
        area.set('title', $(args).find('.assoc_obj_title').html());
        area.set('link', $(args).attr('data-beid'));
        area.set('background', $(args).find('.rel_uri').val());
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

    $('#relationType_mediamap .obj').first().find('.assoc_obj_title').click();
})