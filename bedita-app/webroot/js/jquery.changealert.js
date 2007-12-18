//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
/*
Handler per la gestione del cambiamento nelle pagine.
*/
var _changed = false ;
var _elementAlert = null ;

var html = " \
		<span id='_hndVisualAlert'></span> \
		<input type='checkbox' id='_hndChkbox'> \
		<a id='_cmdCheck' href='#'>Segnala</a> \
		<br/> \
		Seleziona qui se vuoi essere avvertito del cambiamento dei dati quando lasci la pagina. \
" ;

/*
Scrive nel primo elemento trovato il codice per visualizzare l'avvenuto cambiamento
dei dati
*/
(function() {
	jQuery.fn.changeAlert = function(cContainers) {
		// Setup
		_elementAlert = this.eq(0) ;
		
		// Scrive l'html
		this.eq(0).html(html);
		
		// Setta lo stato in base al cookie
		$('#_hndChkbox').get(0).checked = $.cookie("handlerAlert") ;
		
		$("#_cmdCheck").bind("click", function() {
			$('#_hndChkbox').toggleCheck() ; 
			$.cookie("handlerAlert", ($('#_hndChkbox').get(0).checked) ? 1: null) ;
		});
		
		// Catture l'evento di cambiamento settaggio alert e lo mette in cookie
		$("#_hndChkbox").bind("click", function() { $.cookie("handlerAlert", (this.checked ? 1 : null)) ; });
		
		// Cattura l'evento onchange per ogni input, textarea, select
		cContainers.not($('/', this.eq(0))).bind("change", _setChangedAlert) ;
		
	}	
})(jQuery) ;

/*
Indica a quali elementi va associato il controllo di uscita pagina tramite evento click
*/
(function() {
	jQuery.fn.alertUnload = function(cContainers) {
			
		this.not($('a',_elementAlert)).bind("click", function(event) { 
				if(_changed && $('#_hndChkbox', _elementAlert).get(0).checked) {
					var msg = "" ;
					try {
					msg = changeAlertMessage ;
					} catch(e) {
						msg = "i cambiamenti fatti andranno perduti.\nVuoi continuare?" ;
					}
					if(!confirm(msg)) {
						try {
						event.cancelBubble = true ;
						event.stopPropagation();
						} catch(e) {}
						
						return false ;
					}
				}
				return true ;
		}) ;
	}	
})(jQuery) ;


/*
Funzione che puo' essere chiamata per forzare il segnale di un cambiamento
*/
(function() {
	jQuery.fn.alertSignal = function() {
		_setChangedAlert() ;
	}	
})(jQuery) ;


// Indica l'avvenuto cambiamento
function _setChangedAlert() {
	try {
		if(_changed) return ;
		else _changed = true ;

		$("#_hndVisualAlert").attr('class', 'alertChanged');
		$("#_hndVisualAlert").html("* dati cambiati<br />") ;
	} catch(e) {}
}





