$(document).ready(function() {
    openAtStart("#title,#description");
}

    var opts = {
        container : 'epiceditor',
        textarea : null,
        basePath : 'epiceditor',
        clientSideStorage : true,
        localStorageName : 'epiceditor',
        useNativeFullscreen : true,
        parser : marked,
        file : {
            name : 'epiceditor',
            defaultContent : '',
            autoSave : 100
        },
        theme : {
            base : '/wikidocs/css/epiceditor/base/epiceditor.css',
            preview : '/wikidocs/css/epiceditor/preview/preview-dark.css',
            editor : '/wikidocs/css/epiceditor/editor/epic-dark.css'
        },
        button : {
            preview : true,
            fullscreen : true,
            bar : "auto"
        },
        focusOnLoad : false,
        shortcut : {
            modifier : 18,
            fullscreen : 70,
            preview : 80
        },
        string : {
            togglePreview : 'Toggle Preview Mode',
            toggleEdit : 'Toggle Edit Mode',
            toggleFullscreen : 'Enter Fullscreen'
        },
        autogrow : false
    }
    var editor = new EpicEditor(opts).load();
});
