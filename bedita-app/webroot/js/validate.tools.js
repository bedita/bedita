/*
*	Validation rules.
*	Add methods to jquery validation plugin. 
*	Require validation plugin
*
*	@author: a.pagliarini@channelweb.it
*/


/**
*	return a JSON object with day, month and year
*	
*	@params dateFormat, dateValue
*			the dateFormat has to be in the following format: dd=day, mm=month, yyyy=year 
*			example dateFormat='dd/mm/yyyy'
*/
function getDMY(dateFormat, dateValue) {
	dd_start = dateFormat.indexOf('dd');
	mm_start = dateFormat.indexOf('mm');
	yyyy_start = dateFormat.indexOf('yyyy');
	dd = dateValue.substr(dd_start,2);
	mm = dateValue.substr(mm_start,2);
	yyyy = dateValue.substr(yyyy_start,4);
	return {"day": dd, "month": mm, "year": yyyy};
}


// add validation methods

/*
*	check the date validity
*
*	@params dateFormat
*/
jQuery.validator.addMethod("checkDate", function(value, element, dateFormat) {

	strReg = dateFormat.replace('dd','[0-9]{2}').replace('mm','[0-9]{2}').replace('yyyy','[0-9]{4}');
    reg = new RegExp("^" + strReg + "$");

	if (reg.test(value)) {
		
		d = getDMY(dateFormat,value);
		
		retVal = true;
		
		if (    (d.day > 31 || d.day==0) || (d.month > 12 || d.month==0) ||
		        (d.day==31 && (d.month==2 || d.month==4 || d.month==6 || d.month==9 || d.month==11) ) ||
		        (d.day >29 && d.month==2) ||
		        (d.day==29 && (d.month==2) && ((d.year%4 > 0) || (d.year%4==0 && d.year%100==0 && d.year%400>0 )) )) {
		   retVal = false;
		}
		
		return retVal;

	} else return this.optional(element);
}, jQuery.format("Please enter a valid date in the {0} format"));


/*
*	check if date2 >= date1
*
*	@params params = Array(dateFormat, idRif)
*/
jQuery.validator.addMethod("dateGreaterThen", function(value, element, params) {

	if (params) {
		var dateFormat = params[0];
		var idRef = params[1];
		var dd_start, mm_start, yyyy_start;
		
		// date major
		d2 = getDMY(dateFormat,value);
		date_2 = new Date(d2.year, d2.month, d.day);
		
		//date minor
		d1 = getDMY(dateFormat, $("#"+idRef).val());
		date_1 = new Date(d1.year, d1.month, d1.day);
		
		// set params[2] for message
		params[2] = $("#"+idRef).val();
		
		retVal = true;
		if (date_1.getTime() > date_2.getTime()) {
			retVal = false;
		}
		
		return retVal;
	} else {
		return this.optional(element);
	}

}, jQuery.format("Please enter a date greater than {2}."));


jQuery.validator.addMethod("checkTime", function(value, element, params) {
	if (value) {
		retVal = false;
		var strReg = "(2[0-3]|1\\d|0\\d)[:][0-5]\\d([:][0-5]\\d|\\b)";
    	var reg = new RegExp("^" + strReg + "$");
		if (reg.test(value)) {
			retVal = true;
		}		
		return retVal;

	} else return this.optional(element);

}, jQuery.format("Please enter a valid time in the hh:mm format"));