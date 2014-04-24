/**
 *  areas custom js
 */

var priorityOrder = 'asc';
var contentStartPriority = 1;
var sectionStartPriority = 1;

function addObjToAssocLeafs(url, postdata) {
    $.post(url, postdata, function(html) {
        if (priorityOrder == 'asc') {
            $("#areacontentC tr:last").after(html);
        } else {
            var beforeInsert = parseInt($("#areacontentC tr:not(#noContents)").length);
            $("#areacontentC tr:first").before(html);
            var afterInsert = parseInt($("#areacontentC tr:not(#noContents)").length);
            if (beforeInsert > 0) {
                contentStartPriority = contentStartPriority + (afterInsert - beforeInsert);
            }
        }

        if ($("#noContents")) {
            $("#noContents").hide();
        }
        $("#areacontentC").fixItemsPriority(contentStartPriority);
        $("#areacontentC table").find("tbody").sortable("refresh");
    });
}

$(document).ready(function() {

    priorityOrder = $('input[name=data\\[priority_order\\]]:checked').val();
    var startPriority;
    if ($("#areacontentC").find("input[name*='[priority]']:first").length) {
        contentStartPriority = parseInt($("#areacontentC").find("input[name*='[priority]']:first").val());
    }
    if ($("#areasectionsC").find("input[name*='[priority]']:first").length) {
        sectionStartPriority = parseInt($("#areasectionsC").find("input[name*='[priority]']:first").val());
    }

    $("#areacontentC table").find("tbody").sortable({
        distance: 20,
        opacity:0.7,
        update: function() {
            $(this).fixItemsPriority(contentStartPriority);
        }
    }).css("cursor","move");

    $(".newcontenthere").click(function() {
        var urltogo = $('.selectcontenthere').val();
        window.location.href = urltogo;
        return false;
    });

    $("#areasectionsC table").find("tbody").sortable ({
        distance: 20,
        opacity:0.7,
        update: function() {
            $(this).fixItemsPriority(sectionStartPriority);
        }
    }).css("cursor","move");

    // reorder contents and sections on the fly changing priority
    $('input[name=data\\[priority_order\\]]').click(function() {
        priorityOrder = $('input[name=data\\[priority_order\\]]:checked').val();

        $("#areacontentC table").find("tbody").each(function(elem,index){
            var arr = $.makeArray($("tr", this).detach());
            arr.reverse();
            $(this).append(arr);
        });
        $("#areasectionsC table").find("tbody").each(function(elem,index){
            var arr = $.makeArray($("tr", this).detach());
            arr.reverse();
            $(this).append(arr);
        });

        var firstInputContent = $("#areacontentC").find("input[name*='[priority]']:first");
        contentStartPriority = (firstInputContent.length)? parseInt(firstInputContent.val()) : 1;
        var firstInputSection = $("#areacontentS").find("input[name*='[priority]']:first");
        sectionStartPriority = (firstInputSection.length)? parseInt(firstInputSection.val()) : 1;
    });

    $(document).on('click', '#areacontentC .remove, #areasectionsC .remove', function() {
        var contentField = $("#contentsToRemove").val() + $(this).parents("tr:first").find("input[name*='[id]']").val() + ",";
        $("#contentsToRemove").val(contentField);
        var itemToUpdate = $(this).parents('.htabcontent:first');

        $(this).parents("tr:first").remove();

        var startPriority = 1;
        if (itemToUpdate[0] === $('#areacontentC')[0]) {
            startPriority = contentStartPriority;
        } else if (itemToUpdate[0] === $('#areasectionsC')[0]) {
            startPriority = sectionStartPriority;
        }

        if ($("#areacontentC tr:visible, #areasectionsC tr:visible").not('#noContents').length == 0) {
            $("#noContents").show();
        }

        itemToUpdate.fixItemsPriority(startPriority);
    });

});