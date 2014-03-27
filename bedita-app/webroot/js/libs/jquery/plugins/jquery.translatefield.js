/*
 * Aggiunge il  codice per la traduzione delle proprieta' testuali dei diversi oggetti
 *
 * @author		giangi@qwerg.com
 */

/*
Inserimento di versioni linguistiche di campi specificati
cmd			Comando che ha richiesto la traduzione
name		Nome della proprieta' da tradurre
langs		Elenco delle lingue selezioanbili
*/
jQuery.addTranslateField = function(cmd, name, langs) {
	// Create a link to self
	var me  = this;		
	
	var counter = 1000 ;	// indice assegnato ad ogni nuovo campo
	
	// Aggiunge l'evento onclick sul comando
	$(cmd)
	.bind("click", function() {
		
		// aggiunge un campo per il nuovo testo e il select per la lingua
		me.addField($(this)) ;
		
	}) ;
	
	// visualizza il menu select delle lingue
	this.addField  = function (cmd) {
		
		// Crea il pulldown select
		var select = $("<select></select>") ;		
		
		for(key in langs) {			
			var tmp = $("<option>").attr("value", key).append(langs[key]) ;
			select.append(tmp) ;
		}
		
		// crea il comando per la cancellazione della riga
		var cmdDelete = $("<input type=\"button\" name=\"delete\" value=\" x \">").bind("click", function() {
			$("../..", this).remove() ;
		}) ;
		
		// Clona la riga della tabella di cui fa  parte il comando
		var clone = $("../..", cmd).clone() ;
		
		//Setta il contenuto del clone
		$("TD:nth-child(1)", clone).html("<input type='hidden' value='"+name+"'>") ;		
		$("TD:nth-child(2)//input, TD:nth-child(2)//textarea", clone).val("") ;
		$("TD:nth-child(3)", clone).html("") ;
		
		// Inserisce il select e il comando per la cancellazione
		$("TD:nth-child(3)", clone).append(select) ;
		$("TD:nth-child(3)", clone).append("&nbsp;&nbsp;") ;
		$("TD:nth-child(3)", clone).append(cmdDelete) ;
		
		// Definisce i nomi dei diversi campi
		counter++ ;
		$("TD:nth-child(1)//input", clone).attr("name", "data[LangText]["+counter+"][name]") ;
		$("TD:nth-child(2)//input, TD:nth-child(2)//textarea", clone).attr("name", "data[LangText]["+counter+"][text]") ;
		$("TD:nth-child(3)//select", clone).attr("name", "data[LangText]["+counter+"][lang]") ;
		
		// Inserisce il clone
		$("../..", cmd).after(clone) ;
	}
}

jQuery.fn.addTranslateField = function(name, langs) {
	langs = langs || {"it" : "Italiano"};
	
	this.each(function() {
		new jQuery.addTranslateField(this, name, langs);
	});

	// Don't break the chain
	return this;
}


