/*
Funzioni comuni ai form di tutti i moduli
*/

// Rende visibili tutti i blocchi di form messi dentro a DIV di classe 'blockForm'
function showAllBlockPage() {
	$("DIV[@class='blockForm']").show() ;
	$("#openAllBlockLabel").hide() ;
	$("#closeAllBlockLabel").show() ;
}

// Nasconde tutti i blocchi di form messi dentro a DIV di classe 'blockForm'
function hideAllBlockPage() {
	$("DIV.blockForm").hide() ;	
	$("#closeAllBlockLabel").hide() ;
	$("#openAllBlockLabel").show() ;
}

