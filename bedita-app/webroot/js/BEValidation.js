/*
Utlizzato per la validazione dei campi di un form.
DEVE ESSERE INCLUSO:
yav.js e yav-config-...js
*/


var BEValidation = {
	// settaggi per passati a yav
	inputhighlight 		: true,				// if you want yav to highligh fields with errors
	inputclasserror 	: 'inputError',		// classname you want for the error highlighting
	inputclassnormal 	: 'inputNormal',	// classname you want for your fields without highlighting
	innererror 			: 'innerError',		// classname you want for the inner html highlighting
	errorsdiv 			: 'errorsDiv',		// div name where errors will appear (or where jsVar variable is dinamically defined)
	debugmode 			: false,			// if you want yav to alert you for javascript errors (only for developers)
	trimenabled 		: true,				// if you want yav to trim the strings
	
	alertType	: 'classic',				// Valori possibili: classic, innerHtml, jsVar
	fields		: new Array()				// Dive vengono inserite le regole per i diversi form
} ;

/*
Inserisce una nuova regola per un form specifico
*/
BEValidation.addField = function (IDForm, YavRule) {
	if(this.fields[IDForm] == undefined) this.fields[IDForm] = new Array() ;
	this.fields[IDForm][this.fields[IDForm].length] = YavRule ;
}

BEValidation.validate = function (IDForm, alertType) {
	// Se non e' presente il form torna false
	if(this.fields[IDForm] == undefined) return false ;	
	
	// Inserisce le variabili di configurazione
	inputhighlight 		= BEValidation.inputhighlight ;
	inputclasserror 	= BEValidation.inputclasserror ;
	inputclassnormal 	= BEValidation.inputclassnormal ;
	innererror 			= BEValidation.innererror ;
	errorsdiv 			= BEValidation.errorsdiv ;
	debugmode 			= BEValidation.debugmode ;
	trimenabled 		= BEValidation.trimenabled ;
	
	return performCheck(IDForm, this.fields[IDForm], ((alertType)?alertType:this.alertType)) ;
}


				