/*
*	Validation rules.
*	Add methods to jquery validation plugin. 
*	Require validation plugin
*
*	@author: a.pagliarini@channelweb.it
*/

if (!dateFormat) var dateFormat = "dd/mm/yyyy";

jQuery.validator.addMethod("checkDate", function(value,element) {

	strReg = dateFormat.replace('dd','[0-9]{2}').replace('mm','[0-9]{2}').replace('yyyy','[0-9]{4}');
    reg = new RegExp("^" + strReg + "$");

	if (reg.test(value)) {
		
		var dd,mm,yyyy,dd_start,mm_start,yyyy_start;
		dd_start = dateFormat.indexOf('dd');
		mm_start = dateFormat.indexOf('mm');
		yyyy_start = dateFormat.indexOf('yyyy');
		dd = value.substr(dd_start,2);
		mm = value.substr(mm_start,2);
		yyyy = value.substr(yyyy_start,4);
		
		retVal = true;
		
		if (    (dd > 31 || dd==0) || (mm > 12 || mm==0) ||
		        (dd==31 && (mm==2 || mm==4 || mm==6 || mm==9 || mm==11) ) ||
		        (dd >29 && mm==2) ||
		        (dd==29 && (mm==2) && ((yyyy%4 > 0) || (yyyy%4==0 && yyyy%100==0 && yyyy%400>0 )) )) {
		   retVal = false;
		}
		
		return retVal;

	} else return this.optional(element);
}, "it has to be a valid and well formatted date (" +  dateFormat + ")");
