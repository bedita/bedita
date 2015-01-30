$(document).ready(function() {

    var clientStore = (BEDITA.id) ? true : false;
    var storageName = (BEDITA.id) ? 'epiceditor-' + BEDITA.id : 'epiceditor';

    var opts = {
        container : 'md-editor',
        textarea : 'md-textarea',
        basePath : BEDITA.webroot,
        clientSideStorage : clientStore,
        localStorageName : storageName,
        useNativeFullscreen : true,
        parser : marked,
        file : {
            name : 'epiceditor',
            defaultContent : '',
            autoSave : 100
        },
        theme : {
            base : 'css/epiceditor/base/epiceditor.css',
            preview : 'css/epiceditor/preview/github.css',
            editor : 'css/epiceditor/editor/epic-light.css'
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
    editor.preview();
});
