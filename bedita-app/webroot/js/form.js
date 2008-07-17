/*
Funzioni comuni ai form di tutti i moduli
*/

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

