$(document).ready(function() {

    var uploadMediaCreated = [];

    var dropzone = new Dropzone("form#dropzone-modal", {
        uploadMultiple: false,
        paramName: 'Filedata'
    });

    dropzone.on('success', function(file, response) {
        if (typeof response != 'undefined' && response.id) {
            uploadMediaCreated.push(response.id);
        }
    });

    dropzone.on('error', function(file, errorMessage, xhr) {
        if (typeof errorMessage != 'undefined' && errorMessage.errorMsg) {
            $(file.previewElement).find('.dz-error-message').html(errorMessage.errorMsg);
        }
    });

    dropzone.on('complete', function(file) {
        // File finished uploading, and there aren't any left in the queue.
        if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
            loadObjToAssoc(1, uploadMediaCreated);
        }
    });

});