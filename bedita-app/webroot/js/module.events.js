/**
events custom js
*/

$(document).ready(function(){
	
    if (BEDITA.action == 'view') {

        // calendar events 
        var timePickerOptions = {
            minTime: '00:00',
            maxTime: '23:30',
            timeFormat: 'G:i'
        }

    	$('.timeStart, .timeEnd', '.daterow').timepicker(timePickerOptions);

        var numDates = $('.daterow').length;

        $(".dateremove").click(function () {
    		var row = $(this).parent(".daterow");
    		if ($(".daterow").size() > 1) {
    	        $(row).remove();			
    		} else {
    			row.find(".eventStart").val("");
                row.find(".timeStart").val("");			
                row.find(".eventEnd").val("");
                row.find(".timeEnd").val("");
    		}

    		row = $(this).parent(".newdaterow");
    		$(row).remove();			
    	});

    	$(".dateadd").click(function (){
            var row = $(this).parent(".daterow");
            if (row.length == 0) {
            	row = $(this).parent(".newdaterow");
            }
            var newRow = $(".dummydaterow").clone(true);
            newRow.insertAfter(row);
            newRow.removeClass("dummydaterow").addClass("newdaterow");
            var evtStart = newRow.find(".eventStart");
            evtStart.addClass("dateinput");

            // for newly created objs numDates may be 0
            if (numDates == 0) {
            	numDates = 1;
            }

            evtStart.prop("id", "eventStart_" + numDates);
            evtStart.prop("name", "data[DateItem][" + numDates + "][start_date]");
            var timeStart = newRow.find(".timeStart")
            timeStart.prop("id", "timeStart_" + numDates);
            timeStart.prop("name", "data[DateItem][" + numDates + "][timeStart]");
            var evtEnd = newRow.find(".eventEnd");
            evtEnd.addClass("dateinput");

            evtEnd.prop("id", "eventEnd_" + numDates);
            evtEnd.prop("name", "data[DateItem][" + numDates + "][end_date]");
            var timeEnd = newRow.find(".timeEnd")
            timeEnd.prop("id", "timeEnd_" + numDates);
            timeEnd.prop("name", "data[DateItem][" + numDates + "][timeEnd]");
            numDates++;
            newRow.find(".timeStart, .timeEnd").timepicker(timePickerOptions);
            newRow.find("input.dateinput").datepicker();
    	});

        $(".radioAlways").click(function (){
        	var always = $(this).val();
            if (always == "true") {
            	$(this).closest(".daterow").find("input[type=checkbox]").prop('disabled', true);
                $(this).closest(".daterow").find("input[type=checkbox]").prop('checked', false);
                $(this).closest(".daterow").find(".date_exceptions").hide();

            } else {
                $(this).closest(".daterow").find("input[type=checkbox]").prop('disabled', false);
                $(this).closest(".daterow").find(".date_exceptions").show();
            }
        });

    }
	
});