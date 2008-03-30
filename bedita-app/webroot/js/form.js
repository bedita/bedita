/*
Funzioni comuni ai form di tutti i moduli
*/

// Rende visibili tutti i blocchi di form messi dentro a DIV di classe 'blockForm' escluso quello degli errori
function showAllBlockPage() {
	$("DIV[@class='blockForm']:not('#errorForm')").show() ;
	$("#openAllBlockLabel").hide() ;
	$("#closeAllBlockLabel").show() ;
}

// Nasconde tutti i blocchi di form messi dentro a DIV di classe 'blockForm'
function hideAllBlockPage() {
	$("DIV.blockForm").hide() ;	
	$("#closeAllBlockLabel").hide() ;
	$("#openAllBlockLabel").show() ;
}

// remove blanks, use in onkeyup event
function cutBlank(elem) {
	if (elem.value.length > 0) {
		var i = elem.value.length - 1;
		var c = elem.value.charAt(i);
		if (c == ' ') {
			elem.value = elem.value.substring(0,i);
		}
	}
}

// auto-grow textareas styled with autogrow class
$('textarea.autogrow').autogrow();