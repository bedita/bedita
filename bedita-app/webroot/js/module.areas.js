/**
 *	areas custom js
 */

 function addObjToAssocLeafs(url, postdata) {
	$.post(url, postdata, function(html){
		if(priorityOrder == 'asc') {
			var startPriority = $("#areacontentC").find("input[name*='[priority]']:first").val();
			$("#areacontentC tr:last").after(html);
		} else {
			var startPriority = parseInt($("#areacontentC").find("input[name*='[priority]']:first").val());
			var beforeInsert = parseInt($("#areacontentC tr").size());
			$("#areacontentC tr:first").before(html);
			var afterInsert = parseInt($("#areacontentC tr").size());
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
	$("#areacontentC").find(".remove").click(function() {
		var contentField = $("#contentsToRemove").val() + $(this).parents("tr:first").find("input[name*='[id]']").val() + ",";
		$("#contentsToRemove").val(contentField);
		var startPriority = $("#areacontentC").find("input[name*='[priority]']:first").val();
		
		if (priorityOrder == "desc" && $(this) != $("#areacontentC").find("input[name*='[priority]']:first")) {
			startPriority--;
		}
		
		$(this).parents("tr:first").remove();
		
		if ($("#areacontentC tr:visible").not('#noContents').length == 0) {
			$("#noContents").show();
		}
		$("#areacontentC").fixItemsPriority(startPriority);
	});
}

$(document).ready(function() {

	if ($("#areacontentC").find("input[name*='[priority]']:first")) {
		var startPriority = $("#areacontentC").find("input[name*='[priority]']:first").val();
	} else {
		var startPriority = 1;
	}

	$("#areacontentC table").find("tbody").sortable({
		distance: 20,
		opacity:0.7,
		update: function() {
			if (priorityOrder == 'desc' && startPriority < $("#areacontentC").find("input[name*='[priority]']:first").val()) {
				startPriority = $("#areacontentC").find("input[name*='[priority]']:first").val();
			}
			$(this).fixItemsPriority(startPriority);
		}
	}).css("cursor","move");
	
	setRemoveActions();
	
	$(".newcontenthere").click(function() {
		var urltogo = $('.selectcontenthere').val();
		window.location.href = urltogo;
		return false;
	});
	
});