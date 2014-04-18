$(document).ready(function() {

    var quickItemsCreated = [];

    /**
     * show or hide input file according to the object type
     */
    function quickItemFileForm() {
        var isMultimedia = $('#addQuickItem select[name=data\\[object_type_id\\]]')
            .find('option:selected')
            .attr('data-multimedia');

        if (isMultimedia) {
            $('#quickitemFileContainer').show();
        } else {
            $('#quickitemFileContainer').hide();
        }
    }

    quickItemFileForm();

    $('#addQuickItem select[name=data\\[object_type_id\\]]').change(function() {
        quickItemFileForm();
    });

    $('#addQuickItem').find('input[type=submit]').click(function() {
        var status = $(this).attr('data-status');
        $('#addQuickItem').find('input[name=data\\[status\\]]').val(status);
    });

    $('#addQuickItem').submit(function(e) {
        e.preventDefault();
        $(this).ajaxSubmit({
            dataType: 'json',
            resetForm: true,
            beforeSubmit: function() {
                $('.quickitem form').hide()
                $('.quickitem').addClass('loader').show();
            },
            success: function(data) {
                $('.quickitem form').show()
                $('.quickitem').removeClass('loader');
                if (typeof data != 'undefined' && data.id) {
                    quickItemsCreated.push(data.id);
                    loadObjToAssoc(1, quickItemsCreated);
                }
                $('#addQuickItem select:not(.areaSectionAssociation)').select2(select2optionsSimple);
                $('#addQuickItem select.areaSectionAssociation').select2(select2optionsTree);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus);
                $('.quickitem form').show()
                $('.quickitem').removeClass('loader');
            }
        });
    });
});