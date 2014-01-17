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
})