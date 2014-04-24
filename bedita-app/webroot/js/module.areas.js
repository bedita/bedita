/**
 *  areas custom js
 */

var priorityOrder = 'asc';

function addObjToAssocLeafs(url, postdata) {
    $.post(url, postdata, function(html) {
        var startPriority = 1;
        if ($("#areacontentC").find("input[name*='[priority]']:first").length) {
            startPriority = parseInt($("#areacontentC").find("input[name*='[priority]']:first").val());
        }
        if (priorityOrder == 'asc') {
            $("#areacontentC tr:last").after(html);
        } else {
            var beforeInsert = parseInt($("#areacontentC tr:not(#noContents)").length);
            $("#areacontentC tr:first").before(html);
            var afterInsert = parseInt($("#areacontentC tr:not(#noContents)").length);
            if (beforeInsert == 0) {
                startPriority = 0;
            }
            startPriority = startPriority + (afterInsert - beforeInsert);
        }

        if ($("#noContents")) {
            $("#noContents").hide();
        }
        $("#areacontentC").fixItemsPriority(startPriority);
        $("#areacontentC table").find("tbody").sortable("refresh");
        setRemoveActions();
    });
}

function setRemoveActions() {
    $("#areacontentC, #areasectionsC").find(".remove").click(function() {
        var contentField = $("#contentsToRemove").val() + $(this).parents("tr:first").find("input[name*='[id]']").val() + ",";
        $("#contentsToRemove").val(contentField);
        var itemToUpdate = $(this).parents('.htabcontent:first');
        var startPriority = itemToUpdate.find("input[name*='[priority]']:first").val();

        if (priorityOrder == "desc" && $(this) != itemToUpdate.find("input[name*='[priority]']:first")) {
            startPriority--;
        }

        $(this).parents("tr:first").remove();

        if ($("#areacontentC tr:visible, #areasectionsC tr:visible").not('#noContents').length == 0) {
            $("#noContents").show();
        }

        itemToUpdate.fixItemsPriority(startPriority);
    });
}

$(document).ready(function() {

    priorityOrder = $('input[name=data\\[priority_order\\]]:checked').val();
    var startPriority;

    $("#areacontentC table").find("tbody").sortable({
        distance: 20,
        opacity:0.7,
        start: function(event, ui) {
            // calculate startPriority
            startPriority = 1;
            if ($("#areacontentC").find("input[name*='[priority]']:first").length) {
                startPriority = $("#areacontentC").find("input[name*='[priority]']:first").val();
            }
        },
        update: function() {
            $(this).fixItemsPriority(startPriority);
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
        start: function(event, ui) {
            // calculate startPriority
            startPriority = 1;
            if ($("#areasectionsC").find("input[name*='[priority]']:first").length) {
                startPriority = $("#areasectionsC").find("input[name*='[priority]']:first").val();
            }
        },
        update: function() {
            $(this).fixItemsPriority(startPriority);
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
    });

    setRemoveActions();
});