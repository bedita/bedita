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


    if (BEDITA.action == 'calendar') {

        // toolbar buttons -> form submit
        var d1 = new Date($('#event-calendar-form .start_date').attr('data-date-iso'));
        var d2 = new Date($('#event-calendar-form .end_date').attr('data-date-iso'));

        if ( typeof(d1) !== 'undefined' && typeof(d2) !== 'undefined' ) {

            // prev week
            $('.js-calendar-toolbar .js-prev-week').on('click', function(e) {
                e.preventDefault();

                var sd = new Date(d1);
                sd.setDate(sd.getDate() - 7);
                submitCalendarRange(sd, d1);
            });

            // next week
            $('.js-calendar-toolbar .js-next-week').on('click', function(e) {
                e.preventDefault();

                var ed = new Date(d2);
                ed.setDate(ed.getDate() + 7);
                submitCalendarRange(d2, ed);
            });


            // this month
            $('.js-calendar-toolbar .js-this-month').on('click', function(e) {
                e.preventDefault();

                var today = new Date();
                var sd = new Date(today.getFullYear(), today.getMonth(), 1);
                var ed = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                submitCalendarRange(sd, ed);
            });


            // one month from today
            $('.js-calendar-toolbar .js-one-month').on('click', function(e) {
                e.preventDefault();

                var sd = new Date();
                var ed = new Date(sd.getFullYear(), sd.getMonth() + 1, sd.getDate());
                submitCalendarRange(sd, ed);
            });


            // today
            $('.js-calendar-toolbar .js-today').on('click', function(e) {
                e.preventDefault();

                var sd = new Date();
                var ed = new Date();
                submitCalendarRange(sd, ed);
            });


            // define a formatter function
            function formatEventDate(dateObject) {
                    var day = dateObject.getDate().toString();
                    day = day.length > 1 ? day : '0' + day;
                    var month = (dateObject.getMonth() + 1).toString();
                    month = month.length > 1 ? month : '0' + month;

                    return dateObject.getFullYear() + '-' + month + '-' + day;
            }

            // set form and submit
            function submitCalendarRange(sd, ed) {
                $('#event-calendar-form .js-toolbarStartDate').val( formatEventDate(sd) );
                $('#event-calendar-form .js-toolbarEndDate'  ).val( formatEventDate(ed) );
                $('#event-calendar-form').submit();
            }


        }

    }

});
