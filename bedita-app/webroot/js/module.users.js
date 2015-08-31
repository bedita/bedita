// used in view group details

var urlAddObjToAssoc = BEDITA.base + 'pages/loadObjectToAssoc';
var urlUsersInGroup = BEDITA.base + 'users/userInGroupHtml';

/**
 * handle a list of object's ids
 * used to track checked objects also with pagination
 */
var usersToAddToGroupChecked = new ListHandler();

function addObjToAssoc(url, postdata) {
    postdata.tplname = 'elements/form_group_permissions';
    $("#loadingDownloadRel").show();
    $.post(url, postdata, function(html){
        $("#loadingDownloadRel").hide();
        // add row
        $("table.group_objects").append(html);
        $("table.group_objects select[multiple]").chosen({width: '95%'});
    });
}

function loadUsersToAdd(page) {
    var url = $('#addUserToGroupModal').attr('rel');
    $('#listUsers').empty();
    $('#loadUsersInModal').show();
    var filterObj = {},
        search = $.trim($('#search').val());
    if (search.length) {
        filterObj.filter = {query: search};
    }
    filterObj = addCsrfToken(filterObj);
    $.ajax({
        url: url + '/page:' + page,
        data: filterObj,
        dataType: 'html',
        type: 'POST'
    })
    .done(function(html) {
        $('#listUsers').append(html);
        var listIds = usersToAddToGroupChecked.get();
        for (var i in listIds) {
            $('#listUsers').find('.ucheck[value=' + listIds[i] + ']').prop('checked', true);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('error loading users list: ' + textStatus + ', ' + errorThrown);
    })
    .always(function() {
        $('#loadUsersInModal').hide();
    });
}

function addUserToGroup(id) {
    var url = urlUsersInGroup + '/' + id;
    $("#loadingUserInGroup").show();
    $.post(url, {}, function(html) {
        $("#loadingUserInGroup").hide();
        var newTrs = $(html);
        var tbody = $("#users-in-group-table").find("tbody");
        tbody.append( newTrs );
        $('#users-in-group').text( tbody.find('tr').length - 1 );
        $('#fakeSave').val('edited');
        $('.secondacolonna .modules label').addClass('save');
    });
}

$(document).ready(function() {
    $(document).delegate('#listUsers .ucheck', 'change', function() {
        var objectId = $(this).val();
        if ($(this).prop('checked')) {
            usersToAddToGroupChecked.add(objectId);
        } else {
            usersToAddToGroupChecked.remove(objectId);
        }

        var addLabel = $('#addUserToGroup').attr('data-value');

        var countIds = usersToAddToGroupChecked.get().length;
        if (countIds) {
            addLabel += '(' + countIds + ' users )';
        }
        $('#addUserToGroup').val(addLabel);
    });

    $(document).on('click', 'table.group_objects input[name=remove]', function() {
        $(this).parents('tr:first').remove();
        $('#fakeSave').val('edited');
        $('.secondacolonna .modules label').addClass('save');
    });

    $(document).on('click', '.indexlist .commands .remove', function() {
        var table = $(this).closest('tbody');
        $(this).closest('tr').remove();
        $('#users-in-group').text( table.find('tr').length - 1 );
        $('#fakeSave').val('edited');
        $('.secondacolonna .modules label').addClass('save');
    });

    $('#authselect').change(function() {
        var value = $(this).val();
        $('.authTypeForm').hide();
        $('.authTypeForm#authType'+capitaliseFirstLetter(value)).show();
    });

    $(document).on('click', '#addUserToGroup', function() {
        $('#modal').hide();
        $('#modaloverlay').hide();
        var listIds = usersToAddToGroupChecked.get();
        for (var i in listIds) {
            addUserToGroup(listIds[i]);
        }
    });

    $(document).on('click', '#createUser', function() {
        window.open(
            BEDITA.base + 'users/viewUser',
            '_blank'
        );
    });

    $(document).on('click', '#addUserToGroupSearch', function() {
        loadUsersToAdd(1);
    });

    $(document).on('click', '#addUserToGroupClean', function() {
        $('#search').val('');
        loadUsersToAdd(1);
    });

    // handle pagination in 'promote as user' modal
    $(document).on('click', '#user_contents_nav a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var page = $(this).attr('rel');
        loadUsersToAdd(page);
    });

    // save/delete group from user/group view
    $('.bemaincommands').click(function(e) {
        e.preventDefault();
        var buttonName = $(this).prop('name');
        if (buttonName == 'saveUser') {
            $('#userForm').submit();
        } else if (buttonName == 'saveGroup') {
            $('#groupForm').submit();
        } else if (buttonName == 'deleteGroup') {
            var groupName = $('#groupname').val();
            // delGroupDialog is defined in tpl
            if (delGroupDialog(groupName)) {
                $('#groupForm').prop('action', BEDITA.base + 'users/removeGroup');
                $('#groupForm').submit();
            }
        }
    });
});
