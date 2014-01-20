var FlatLanderEditor;

$(window).load(function() {
    FlatLanderEditor = new Flatlander({
        el: $('#advanced-multimedia-editor img')[0]
    });

    FlatLanderEditor.BE_relation_dbschema = function(attr) {
        return {
            from: this.id,
            to: attr.id,
            priority: attr.priority,
            prop: {
                left: parseFloat(parseFloat((attr.x).replace('%','')).toFixed(2)),
                top: parseFloat(parseFloat((attr.y).replace('%','')).toFixed(2)),
                width: parseFloat(parseFloat((attr.width).replace('%','')).toFixed(2)),
                height: parseFloat(parseFloat((attr.height).replace('%','')).toFixed(2)),
                hotspotX: parseFloat(parseFloat((attr.hotspotX||"50").replace('%','')).toFixed(2)),
                hotspotY: parseFloat(parseFloat((attr.hotspotX||"50").replace('%','')).toFixed(2)),
                style: attr.style,
                direction: attr.direction,
                behaviour: attr.behaviour
            }
        }
    }

    FlatLanderEditor.BE_content_dbschema = function(attr) {
        return {
            id: attr.id,
            title: attr.title,
            body: attr.body,
        }
    }

    FlatLanderEditor.getBErelations = function() {
        var obj = this;
        var j = [];
        for(var i in obj.areas) {
            j.push( obj.BE_relation_dbschema(obj.areas[i].attr) );
        }
        return j;
    }

    FlatLanderEditor.getBEcontents = function() {
        var obj = this;
        var j = [];
        for(var i in obj.areas) {
            j.push( obj.BE_content_dbschema(obj.areas[i].attr) );
        }
        return j;
    }

    FlatLanderEditor.bind('areacreated', function(area) {
        $('#relationType_mediamap .modalbutton').click();
    })

    var editorInputs = [
        '<textarea rows="1" type="text" name="number" /></textarea>',
        '<textarea name="title" rows="1"></textarea>',
        '<textarea name="link" rows="1"></textarea>',
        '<input type="file" name="background" />',
        '<select name="style"><option>none</option><option>bordered</option><option>fill</option><option>pointer</option></select>',
        '<select name="behaviour"><option>popup</option><option>popup & zoom</option><option>modal</option></select><label for="direction">Popup direction:</label><select name="direction"><option value="auto">auto</option><option value="n">North</option><option value="w">West</option><option value="e">East</option><option value="s">South</option><option value="nw">North - West</option><option value="ne">North - East</option><option value="sw">South - West</option><option value="se">South - East</option></select>',
        '<textarea rows="8" name="body"></textarea>'
    ];

    for (var i=0; i<editorInputs.length; i++) {
        var o = editorInputs[i];
        FlatLanderEditor.FlatlanderEditorInstance.appendInput({
            el: $(o)[0],
        });
    }

    /*FlatLanderEditor.FlatlanderEditorInstance.appendInput({
        el: $('#relationType_mediamap .modalbutton').clone().attr('name', 'BElink')[0],
        onClick: function(el) {
            $(el).BEmodal();
        }
    });*/
})