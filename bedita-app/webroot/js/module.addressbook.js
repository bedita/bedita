function addUserToCard(id,username) {
    $('#user_id').val(id);
    $('#user_name').text(username);
}

function removeUserFromCard() {
    $('#user_id').val('');
    $('#user_name').text(' - ');
    $('#remove_user').prop('disabled', true);
}

function loadUsersToPromote(page) {
    var url = $('#promoteAsUser').attr('rel');
    $('#listUsers').empty();
    $('#loadUsersInModal').show();
    var filterObj = {};
    var search = $.trim($('#search').val());
    if (search.length) {
        filterObj.filter = {query: search};
    }
    $.ajax({
        url: url + '/page:' + page,
        data: filterObj,
        dataType: 'html',
        type: 'POST'
    })
    .done(function(html) {
        $('#listUsers').append(html)
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('error loading users list: ' + textStatus + ', ' + errorThrown);
    })
    .always(function() {
        $('#loadUsersInModal').hide();
    });
}

$(document).ready(function() {
    $('#remove_user').click(removeUserFromCard);

    $(document).on('click', '#userToCard', function() {
        var id = $('.uradio:checked').val();
        var username = $('.uradio:checked').attr('rel');
        $('#modal').hide();
        $('#modaloverlay').hide();
        addUserToCard(id,username);
    });

    $(document).on('click', '#createUser', function() {
        window.open(
            BEDITA.base + 'users/viewUser',
            '_blank'
        );
    });

    $(document).on('click', '#promoteUserSearch', function() {
        loadUsersToPromote(1);
    });

    $(document).on('click', '#promoteUserClean', function() {
        $('#search').val('');
        loadUsersToPromote(1);
    });

    // handle pagination in 'promote as user' modal
    $(document).on('click', '#user_contents_nav a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var page = $(this).attr('rel');
        loadUsersToPromote(page);
    });
});
