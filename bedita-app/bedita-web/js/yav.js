/***********************************************************************
 * YAV - Yet Another Validator  v1.3.0                                 *
 * Copyright (C) 2005-2006                                             *
 * Author: Federico Crivellaro <f.crivellaro@gmail.com>                *
 * WWW: http://yav.sourceforge.net                                     *
 *                                                                     *
 * This library is free software; you can redistribute it and/or       *
 * modify it under the terms of the GNU Lesser General Public          *
 * License as published by the Free Software Foundation; either        *
 * version 2.1 of the License, or (at your option) any later version.  *
 *                                                                     *
 * This library is distributed in the hope that it will be useful,     *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU   *
 * Lesser General Public License for more details.                     *
 *                                                                     *
 * You should have received a copy of the GNU Lesser General Public    *
 * License along with this library; if not, write to the Free Software *
 * Foundation, Inc.,59 Temple Place,Suite 330,Boston,MA 02111-1307 USA *
 *                                                                     *
 * last revision:  21 SEP 2006                                         *
 ***********************************************************************/

//------------------------------------------------------------ PUBLIC FUNCTIONS
var undef;
var internalRules;
function performCheck(formName, strRules, alertType) {
    var rules = makeRules(strRules);
    internalRules = makeRules(strRules);
    this.f = document.forms[formName];
    if( !this.f ) {
        debug('DEBUG: could not find form object ' + formName);
        return null;
    }
    var errors = new Array();
    var ix = 0;
    if (rules.length) {
        for(var i=0; i<rules.length; i++) {
            var aRule = rules[i];
            if (aRule!=null) {
                highlight(getField(f, aRule.el), inputclassnormal);
            }
        }
    } else {
        if (rules!=null) {
            highlight(getField(f, rules.el), inputclassnormal);
        }
    }
    if (rules.length) {
        for(var i=0; i<rules.length; i++) {
            var aRule = rules[i];
            var anErr = null;
            if (aRule==null) {
                //do nothing
            } else if (aRule.ruleType=='pre-condition' || aRule.ruleType=='post-condition') {
                //do nothing
            } else if (aRule.ruleName=='implies') {
                pre  = aRule.el;
                post = aRule.comparisonValue;
				
                var oldClassName = getField(f, rules[pre].el).className;
                if ( checkRule(f, rules[pre])==null && checkRule(f, rules[post])!=null ) {
                    anErr = aRule.alertMsg;
                } else if ( checkRule(f, rules[pre])!=null ) {
                    getField(f, rules[pre].el).className = oldClassName;
                }
            } else {
                anErr = checkRule(f, aRule);
            }
            if ( anErr!=null ) {
                errors[ix] = anErr;
                ix++;
            }
        }//for
    } else {
        var myRule = rules;
        err = checkRule(f, myRule);
        if ( err!=null ) {
            errors[0] = err;
        }
    }
    return displayAlert(errors, alertType);
}

function checkKeyPress(ev, obj, strRules) {
    var keyCode = null;
    if ( getBrowser()=='msie' ) {
        keyCode = window.event.keyCode;
    } else if ( getBrowser()=='netscape' || getBrowser()=='firefox' ) {
        keyCode = ev.which;
    }
    var rules = makeRules(strRules);
    var keyAllowed = true;
    if (rules.length) {
        for(var i=0; i<rules.length; i++) {
            var aRule = rules[i];
            if (aRule.ruleName=='keypress' && aRule.el==obj.name) {
                keyAllowed = isKeyAllowed(keyCode, aRule.comparisonValue);
                break;
            }
        }
    } else {
        var aRule = rules;
        if (aRule.ruleName=='keypress' && aRule.el==obj.name) {
            keyAllowed = isKeyAllowed(keyCode, aRule.comparisonValue);
        }
    }
    if (!keyAllowed) {
        if ( getBrowser()=='msie' ) {
            window.event.keyCode=0;
        } else if ( getBrowser()=='netscape' || getBrowser()=='firefox' ) {
            ev.initKeyEvent('keypress', true, true, window, false, false, true, false, 0, 0x00, obj);
        }
    }
    return false;
}

//------------------------------------------------------------ PRIVATE FUNCTIONS

function displayAlert(messages, alertType) {
    var retval =null;
    if (alertType=='classic') {
        retval = displayClassic(messages);
    } else if (alertType=='innerHtml') {
        retval = displayInnerHtml(messages);
    }else if (alertType=='jsVar') {
        retval = displayJsVar(messages);
    } else {
        debug('DEBUG: alert type ' + alertType + ' not supported');
    }
    return retval;
}

function displayClassic(messages) {
    var str = '';
    if ( messages!=null && messages.length>0 ) {
    	if (strTrim(HEADER_MSG).length > 0) {
            str += HEADER_MSG + '\n\n';
        }
        for (var i=0; i<messages.length; i++) {
            str += ' ' + messages[i] + '\n';
        }
    	if (strTrim(FOOTER_MSG).length > 0) {
            str += '\n' + FOOTER_MSG;
        }
        alert(str);
        return false;
    } else {
    	return true;
    }
}

function displayInnerHtml(messages) {
    if ( messages!=null && messages.length>0 ) {
        var str = '';
    	if (strTrim(HEADER_MSG).length > 0) {
            str += HEADER_MSG;
        }
        str += '<ul>';
        for (var i=0; i<messages.length; i++) {
            str += '<li>'+messages[i]+'</li>';
        }
        str += '</ul>';
    	if (strTrim(FOOTER_MSG).length > 0) {
            str += FOOTER_MSG;
        }
        document.getElementById(errorsdiv).innerHTML = str;
        document.getElementById(errorsdiv).className = innererror;
        document.getElementById(errorsdiv).style.display = 'block';
        return false;
    } else {
        document.getElementById(errorsdiv).innerHTML = '';
        document.getElementById(errorsdiv).className = '';
        document.getElementById(errorsdiv).style.display = 'none';
        return true;
    }
}

function displayJsVar(messages) {
    document.getElementById(errorsdiv).className = '';
    document.getElementById(errorsdiv).style.display = 'none';
    if ( messages!=null && messages.length>0 ) {
        var str = '';
        str += '<script>var jsErrors;</script>';
        document.getElementById(errorsdiv).innerHTML = str;
        jsErrors = messages;
        return false;
    } else {
        document.getElementById(errorsdiv).innerHTML = '<script>var jsErrors;</script>';
        return true;
    }
}

function rule(el, ruleName, comparisonValue, alertMsg, ruleType) {
    if ( !checkArguments(arguments) ) {
        return false;
    }
    tmp = el.split(':');
    nameDisplayed = '';
    if (tmp.length == 2) {
        nameDisplayed = tmp[1];
        el = tmp[0];
    }
    this.el = el;
    this.nameDisplayed = nameDisplayed;
    this.ruleName = ruleName;
    this.comparisonValue = comparisonValue;
    this.ruleType = ruleType;
    if (alertMsg==undef || alertMsg==null) {
        this.alertMsg = getDefaultMessage(el, nameDisplayed, ruleName, comparisonValue);
    } else {
        this.alertMsg = alertMsg;
    }
}

function checkRule(f, myRule) {
    retVal = null;
    if (myRule != null) {
        if (myRule.ruleName=='custom') {
            var customFunction = ' retVal = ' + myRule.el;
            eval(customFunction);
        } else if (myRule.ruleName=='and') {
            var op_1 = myRule.el;
            var op_next = myRule.comparisonValue;
            if ( checkRule(f, internalRules[op_1])!=null ) {
                retVal = myRule.alertMsg;
                if (myRule.ruleType=='pre-condition') {
                    highlight(getField(f, internalRules[op_1].el), inputclassnormal);
                }
            } else {
                op_k = op_next.split('-');
                for(var k=0; k<op_k.length; k++) {
                    if ( checkRule(f, internalRules[op_k[k]])!=null ) {
                        retVal = myRule.alertMsg;
                        if (myRule.ruleType=='pre-condition') {
                            highlight(getField(f, internalRules[op_k[k]].el), inputclassnormal);
                        }
                        break;
                    }
                }
            }
        } else if (myRule.ruleName=='or') {
            var op_1 = myRule.el;
            var op_next = myRule.comparisonValue;
            var success = false;
            if ( checkRule(f, internalRules[op_1])==null ) {
                success = true;
            } else {
                if (myRule.ruleType=='pre-condition') {
                    highlight(getField(f, internalRules[op_1].el), inputclassnormal);
                }
                op_k = op_next.split('-');
                for(var k=0; k<op_k.length; k++) {
                    if ( checkRule(f, internalRules[op_k[k]])==null ) {
                        success = true;
                        break;
                    } else {
                        if (myRule.ruleType=='pre-condition') {
                            highlight(getField(f, internalRules[op_k[k]].el), inputclassnormal);
                        }
                    }
                }
            }
            if (!success) {
                retVal = myRule.alertMsg;
            }
        } else {
            el = getField(f, myRule.el);
            if (el == null) {
                debug('DEBUG: could not find element ' + myRule.el);
                return null;
            }
            var err = null;
            if(el.type) {
                if(el.type=='hidden'||el.type=='text'||el.type=='password'||el.type=='textarea') {
                    err = checkText(el, myRule);
                } else if(el.type=='checkbox') {
                    err = checkCheckbox(el, myRule);
                } else if(el.type=='select-one') {
                    err = checkSelOne(el, myRule);
                } else if(el.type=='select-multiple') {
                    err = checkSelMul(el, myRule);
                } else if(el.type=='radio') {
                    err = checkRadio(el, myRule);
                } else {
                    debug('DEBUG: type '+ el.type +' not supported');
                }
            } else {
                err = checkRadio(el, myRule);
            }
            retVal = err;
        }
    }
    return retVal;
}

function checkArguments(args) {
    if (args.length < 4) {
        debug('DEBUG: rule requires four arguments at least');
        return false;
    } else if (args[0]==null || args[1]==null) {
        debug('DEBUG: el and ruleName are required');
        return false;
    }
    return true;
}

function checkRadio(el, myRule) {
    var err = null;
    if (myRule.ruleName=='required') {
        var radios = el;
	    var found=false;
	    if (isNaN(radios.length) && radios.checked) {
	    	found=true;
	    } else {
		    for(var j=0; j < radios.length; j++) {
		        if(radios[j].checked) {
		            found=true;
		            break;
		        }
		    }
		}
        if( !found ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='equal') {
        var radios = el;
	    var found=false;
	    if (isNaN(radios.length) && radios.checked) {
	    	if (radios.value==myRule.comparisonValue) {
	    	    found=true;
	    	}
	    } else {
		    for(var j=0; j < radios.length; j++) {
		        if(radios[j].checked) {
        	    	if (radios[j].value==myRule.comparisonValue) {
        	    	    found=true;
                        break;
        	    	}
		        }
		    }
		}
        if( !found ) {
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='notequal') {
        var radios = el;
	    var found=false;
	    if (isNaN(radios.length) && radios.checked) {
	    	if (radios.value!=myRule.comparisonValue) {
	    	    found=true;
	    	}
	    } else {
		    for(var j=0; j < radios.length; j++) {
		        if(radios[j].checked) {
        	    	if (radios[j].value!=myRule.comparisonValue) {
        	    	    found=true;
                        break;
        	    	}
		        }
		    }
		}
        if( !found ) {
            err = myRule.alertMsg;
        }
    } else {
        debug('DEBUG: rule ' + myRule.ruleName + ' not supported for radio');
    }
    return err;
}

function checkText(el, myRule) {
    err = null;
    if (trimenabled) {
    	el.value = strTrim(el.value);
    }
    if (myRule.ruleName=='required') {
        if ( el.value==null || el.value=='' ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='equal') {
        err = checkEqual(el, myRule);
    } else if (myRule.ruleName=='notequal') {
        err = checkNotEqual(el, myRule);
    } else if (myRule.ruleName=='numeric') {
        reg = new RegExp("^[0-9]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='alphabetic') {
        reg = new RegExp("^[A-Za-z]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='alphanumeric') {
        reg = new RegExp("^[A-Za-z0-9]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='alnumhyphen') {
        reg = new RegExp("^[A-Za-z0-9\-_]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='alnumhyphenat') {
        reg = new RegExp("^[A-Za-z0-9\-_@]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='alphaspace') {
        reg = new RegExp("^[A-Za-z0-9\-_ \n\r\t]*$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='email') {
        reg = new RegExp("^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='maxlength') {
        if ( isNaN(myRule.comparisonValue) ) {
            debug('DEBUG: comparisonValue for rule ' + myRule.ruleName + ' not a number');
        }else if ( el.value.length > myRule.comparisonValue ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='minlength') {
        if ( isNaN(myRule.comparisonValue) ) {
            debug('DEBUG: comparisonValue for rule ' + myRule.ruleName + ' not a number');
        } else if ( el.value.length < myRule.comparisonValue ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='numrange') {
        reg = new RegExp("^[-+]{0,1}[0-9]*[.]{0,1}[0-9]*$");
        if ( !reg.test(unformatNumber(el.value)) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        } else {
            regRange = new RegExp("^[0-9]+-[0-9]+$"); 
            if ( !regRange.test(myRule.comparisonValue) ) {
                debug('DEBUG: comparisonValue for rule ' + myRule.ruleName + ' not in format number1-number2');
            } else {
                rangeVal = myRule.comparisonValue.split('-');
                if (eval(unformatNumber(el.value))<eval(rangeVal[0]) || eval(unformatNumber(el.value))>eval(rangeVal[1])) {
                    highlight(el, inputclasserror); 
                    err = myRule.alertMsg;
                }
            }
        }
    } else if (myRule.ruleName=='regexp') {
        reg = new RegExp(myRule.comparisonValue);
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else if (myRule.ruleName=='integer') {
        err = checkInteger(el, myRule);
    } else if (myRule.ruleName=='double') {
        err = checkDouble(el, myRule);
    } else if (myRule.ruleName=='date') {
        err = checkDate(el, myRule);
    } else if (myRule.ruleName=='date_lt') {
        err = checkDateLessThan(el, myRule, false);
    } else if (myRule.ruleName=='date_le') {
        err = checkDateLessThan(el, myRule, true);
    } else if (myRule.ruleName=='keypress') {
        // do nothing
    } else if (myRule.ruleName=='empty') {
        if ( el.value!=null && el.value!='' ) {
            highlight(el, inputclasserror);
            err = myRule.alertMsg;
        }
    } else {
        debug('DEBUG: rule ' + myRule.ruleName + ' not supported for ' + el.type);
    }
    return err;
}

function checkInteger(el, myRule) {
    reg = new RegExp("^[-+]{0,1}[0-9]*$");
    if ( !reg.test(el.value) ) {
        highlight(el, inputclasserror);
        return myRule.alertMsg;
    }
}

function checkDouble(el, myRule) {
    var sep = DECIMAL_SEP;
    reg = new RegExp("^[-+]{0,1}[0-9]*[" + sep + "]{0,1}[0-9]*$");
    if ( !reg.test(el.value) ) {
        highlight(el, inputclasserror);
        return myRule.alertMsg;
    }
}

function checkDate(el, myRule) {
    error = null;
    if (el.value!='') {
        var dateFormat = DATE_FORMAT;
        ddReg = new RegExp("dd");
        MMReg = new RegExp("MM");
        yyyyReg = new RegExp("yyyy");
        if ( !ddReg.test(dateFormat) || !MMReg.test(dateFormat) || !yyyyReg.test(dateFormat)  ) {
            debug('DEBUG: locale format ' + dateFormat + ' not supported');
        } else {
            ddStart = dateFormat.indexOf('dd');
            MMStart = dateFormat.indexOf('MM');
            yyyyStart = dateFormat.indexOf('yyyy');
        }
        strReg = dateFormat.replace('dd','[0-9]{2}').replace('MM','[0-9]{2}').replace('yyyy','[0-9]{4}');
        reg = new RegExp("^" + strReg + "$");
        if ( !reg.test(el.value) ) {
            highlight(el, inputclasserror);
            error = myRule.alertMsg;
        } else {
            dd   = el.value.substring(ddStart, ddStart+2);
            MM   = el.value.substring(MMStart, MMStart+2);
            yyyy = el.value.substring(yyyyStart, yyyyStart+4);
            if ( !checkddMMyyyy(dd, MM, yyyy) ) {
                highlight(el, inputclasserror);
                error = myRule.alertMsg;
            }
        }
    }
    return error;
}

function checkDateLessThan(el, myRule, isEqualAllowed) {
    error = null;
    var isDate = checkDate(el, myRule)==null ? true : false;
    if ( isDate && el.value!='' ) {
        var dateFormat = DATE_FORMAT;
        ddStart = dateFormat.indexOf('dd');
        MMStart = dateFormat.indexOf('MM');
        yyyyStart = dateFormat.indexOf('yyyy');
        dd   = el.value.substring(ddStart, ddStart+2);
        MM   = el.value.substring(MMStart, MMStart+2);
        yyyy = el.value.substring(yyyyStart, yyyyStart+4);
        myDate = "" + yyyy + MM + dd;
        strReg = dateFormat.replace('dd','[0-9]{2}').replace('MM','[0-9]{2}').replace('yyyy','[0-9]{4}');
        reg = new RegExp("^" + strReg + "$");
        var isMeta = myRule.comparisonValue.indexOf('$')==0 
            ? true
            : false;
        var comparisonDate = '';
        if (isMeta) {
            toSplit = myRule.comparisonValue.substr(1);
            tmp = toSplit.split(':');
            if (tmp.length == 2) {
                comparisonDate = this.getField(f, tmp[0]).value;
            } else {
                comparisonDate = this.getField(f, myRule.comparisonValue.substr(1)).value;
            }
        } else {
            comparisonDate = myRule.comparisonValue;
        }
        if ( !reg.test(comparisonDate) ) {
            highlight(el, inputclasserror);
            error = myRule.alertMsg;
        } else {
            cdd   = comparisonDate.substring(ddStart, ddStart+2);
            cMM   = comparisonDate.substring(MMStart, MMStart+2);
            cyyyy = comparisonDate.substring(yyyyStart, yyyyStart+4);
            cDate = "" + cyyyy + cMM + cdd;
            if (isEqualAllowed) {
                if ( !checkddMMyyyy(cdd, cMM, cyyyy) || myDate>cDate ) {
                    highlight(el, inputclasserror);
                    error = myRule.alertMsg;
                }
            } else {
                if ( !checkddMMyyyy(cdd, cMM, cyyyy) || myDate>=cDate ) {
                    highlight(el, inputclasserror);
                    error = myRule.alertMsg;
                }
            }
        }
    } else {
        if ( el.value!='' ) {
            highlight(el, inputclasserror);
            error = myRule.alertMsg;
        }
    }
    return error;
}

function checkEqual(el, myRule) {
    error = null;
    var isMeta = myRule.comparisonValue.indexOf('$')==0 
        ? true
        : false;
    var comparisonVal = '';
    if (isMeta) {
        toSplit = myRule.comparisonValue.substr(1);
        tmp = toSplit.split(':');
        if (tmp.length == 2) {
            comparisonVal = this.getField(f, tmp[0]).value;
        } else {
            comparisonVal = this.getField(f, myRule.comparisonValue.substr(1)).value;
        }
    } else {
        comparisonVal = myRule.comparisonValue;
    }
    if ( el.value!=comparisonVal ) {
        highlight(el, inputclasserror);
        error = myRule.alertMsg;
    }
    return error;
}

function checkNotEqual(el, myRule) {
    error = null;
    var isMeta = myRule.comparisonValue.indexOf('$')==0 
        ? true
        : false;
    var comparisonVal = '';
    if (isMeta) {
        toSplit = myRule.comparisonValue.substr(1);
        tmp = toSplit.split(':');
        if (tmp.length == 2) {
            comparisonVal = this.getField(f, tmp[0]).value;
        } else {
            comparisonVal = this.getField(f, myRule.comparisonValue.substr(1)).value;
        }
    } else {
        comparisonVal = myRule.comparisonValue;
    }
    if ( el.value==comparisonVal ) {
        highlight(el, inputclasserror);
        error = myRule.alertMsg;
    }
    return error;
}

function checkddMMyyyy(dd, MM, yyyy) {
    retVal = true;
    if (    (dd > 31) || (MM > 12) ||
            (dd==31 && (MM==2 || MM==4 || MM==6 || MM==9 || MM==11) ) ||
            (dd >29 && MM==2) ||
            (dd==29 && (MM==2) && ((yyyy%4 > 0) || (yyyy%4==0 && yyyy%100==0 && yyyy%400>0 )) )) {
       retVal = false;
    }
    return retVal;
}

function checkCheckbox(el, myRule) {
    if (myRule.ruleName=='required') {
        if ( !el.checked ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='equal') {
        if ( !el.checked || el.value!=myRule.comparisonValue ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='notequal') {
        if ( !el.checked || el.value==myRule.comparisonValue ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else {
        debug('DEBUG: rule ' + myRule.ruleName + ' not supported for ' + el.type);
    }
}

function checkSelOne(el, myRule) {
    if (myRule.ruleName=='required') {
        var found = false;
        var inx = el.selectedIndex;
        if(inx>=0 && el.options[inx].value) {
            found = true;
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='equal') {
        var found = false;
        var inx = el.selectedIndex;
        if(inx>=0 && el.options[inx].value==myRule.comparisonValue) {
            found = true;
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='notequal') {
        var found = false;
        var inx = el.selectedIndex;
        if(inx>=0 && el.options[inx].value!=myRule.comparisonValue) {
            found = true;
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else {
        debug('DEBUG: rule ' + myRule.ruleName + ' not supported for ' + el.type);
    }
}

function checkSelMul(el, myRule) {
    if (myRule.ruleName=='required') {
        var found = false;
        opts = el.options;
        for(var i=0; i<opts.length; i++) {
            if(opts[i].selected && opts[i].value) {
                found = true;
                break;
            }
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='equal') {
        var found = false;
        opts = el.options;
        for(var i=0; i<opts.length; i++) {
            if(opts[i].selected && opts[i].value==myRule.comparisonValue) {
                found = true;
                break;
            }
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else if (myRule.ruleName=='notequal') {
        var found = false;
        opts = el.options;
        for(var i=0; i<opts.length; i++) {
            if(opts[i].selected && opts[i].value!=myRule.comparisonValue) {
                found = true;
                break;
            }
        }
        if ( !found ) {
            highlight(el, inputclasserror);
            return myRule.alertMsg;
        }
    } else {
        debug('DEBUG: rule ' + myRule.ruleName + ' not supported for ' + el.type);
    }
}

function debug(msg) {
   if (debugmode) {
        alert(msg);
   }
}

function strTrim(str) {
    return str.replace(/^\s+/,'').replace(/\s+$/,'');
}

function makeRules(strRules) {
    var rules=new Array();
    if (strRules.length) {
        for(var i=0; i<strRules.length; i++) {
            rules[i] = splitRule(strRules[i]);
        }
    } else {
        rules[0] = splitRule(strRules);
    }
    return rules;
}

function splitRule(strRule) {
    var retval = null;
    if (strRule!=undef) {
        params = strRule.split('|');
        switch (params.length) {
            case 2:
                retval = new rule(params[0], params[1], null, null, null);
                break;
            case 3:
                if (threeParamRule(params[1])) {
                    retval = new rule(params[0], params[1], params[2], null, null);
                } else if (params[2]=='pre-condition' || params[2]=='post-condition') {
                    retval = new rule(params[0], params[1], null, 'foo', params[2]);
                } else {
                    retval = new rule(params[0], params[1], null, params[2], null);
                }
                break;
            case 4:
                if (threeParamRule(params[1]) && (params[3]=='pre-condition' || params[3]=='post-condition')) {
                    retval = new rule(params[0], params[1], params[2], 'foo', params[3]);
                } else {
                    retval = new rule(params[0], params[1], params[2], params[3], null);
                }
                break;
            default:
                debug('DEBUG: wrong definition of rule');
        }
    }
    return retval;
}

function threeParamRule(ruleName) {
    return (ruleName=='equal' || ruleName=='notequal' || ruleName=='minlength' || ruleName=='maxlength' || ruleName=='date_lt' || ruleName=='date_le' || ruleName=='implies' || ruleName=='regexp' || ruleName=='numrange' || ruleName=='keypress' || ruleName=='and' || ruleName=='or')
        ? true
        : false;
}

function highlight(el, clazz) {
    if (el!=undef && inputhighlight) {
        el.className = clazz;
    }
}

function getDefaultMessage(el, nameDisplayed, ruleName, comparisonValue) {
    if (nameDisplayed.length == 0) {
        nameDisplayed = el;
    }
    var msg = DEFAULT_MSG;
    if (ruleName=='required') {
        msg = REQUIRED_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='minlength') {
        msg = MINLENGTH_MSG.replace('{1}', nameDisplayed).replace('{2}', comparisonValue);
    } else if (ruleName=='maxlength') {
        msg = MAXLENGTH_MSG.replace('{1}', nameDisplayed).replace('{2}', comparisonValue);
    } else if (ruleName=='numrange') {
        msg = NUMRANGE_MSG.replace('{1}', nameDisplayed).replace('{2}', comparisonValue);
    } else if (ruleName=='date') {
        msg = DATE_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='numeric') {
        msg = NUMERIC_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='integer') {
        msg = INTEGER_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='double') {
        msg = DOUBLE_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='equal') {
        msg = EQUAL_MSG.replace('{1}', nameDisplayed).replace('{2}', getComparisonDisplayed(comparisonValue));
    } else if (ruleName=='notequal') {
        msg = NOTEQUAL_MSG.replace('{1}', nameDisplayed).replace('{2}', getComparisonDisplayed(comparisonValue));
    } else if (ruleName=='alphabetic') {
        msg = ALPHABETIC_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='alphanumeric') {
        msg = ALPHANUMERIC_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='alnumhyphen') {
        msg = ALNUMHYPHEN_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='alnumhyphenat') {
        msg = ALNUMHYPHENAT_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='alphaspace') {
        msg = ALPHASPACE_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='email') {
        msg = EMAIL_MSG.replace('{1}', nameDisplayed);
    } else if (ruleName=='regexp') {
        msg = REGEXP_MSG.replace('{1}', nameDisplayed).replace('{2}', comparisonValue);
    } else if (ruleName=='date_lt') {
        msg = DATE_LT_MSG.replace('{1}', nameDisplayed).replace('{2}', getComparisonDisplayed(comparisonValue));
    } else if (ruleName=='date_le') {
        msg = DATE_LE_MSG.replace('{1}', nameDisplayed).replace('{2}', getComparisonDisplayed(comparisonValue));
    } else if (ruleName=='empty') {
        msg = EMPTY_MSG.replace('{1}', nameDisplayed);
    }
    return msg;
}

function getComparisonDisplayed(comparisonValue) {
    comparisonDisplayed = comparisonValue;
    if (comparisonValue.substring(0, 1)=='$') {
        comparisonValue = comparisonValue.substring(1, comparisonValue.length);
        tmp = comparisonValue.split(':');
        if (tmp.length == 2) {
            comparisonDisplayed = tmp[1];
        } else {
            comparisonDisplayed = comparisonValue;
        }
    }
    return comparisonDisplayed;
}

function getBrowser() {
    brs=navigator.userAgent.toLowerCase();
    var retval;
    if (brs.search(/msie\s(\d+(\.?\d)*)/)!=-1) {
        retval='msie';
    } else if (brs.search(/netscape[\/\s](\d+([\.-]\d)*)/)!=-1) {
        retval='netscape';
    } else if (brs.search(/firefox[\/\s](\d+([\.-]\d)*)/)!=-1) {
        retval='firefox';
    } else {
        retval='unknown';
    }
    return retval;
}

function isKeyAllowed(keyCode, charsAllowed) {
    retval = false;
    var aCharCode;
    if (keyCode==8) {
        retval = true;
    } else {
        for(var i=0; i<charsAllowed.length; i++) {
            aCharCode = charsAllowed.charCodeAt(i);
            if (aCharCode==keyCode) {
                retval = true;
                break;
            }
        }
    }
    return retval;
}

function getField(formObj, fieldName){
	var retval = null;
	if (formObj.elements[fieldName]){
		retval = formObj.elements[fieldName];
	}else if (document.getElementById(fieldName)){
		retval = document.getElementById(fieldName);
	}
	return retval;
}

function unformatNumber(viewValue){
    var retval = viewValue.replace(THOUSAND_SEP, ""); 
    retval = retval.replace(DECIMAL_SEP, ".");
    return retval;
}
//end
