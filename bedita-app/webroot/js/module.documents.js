/*
File con la logica utilizzata nel modulo documenti.

@author		giangi@qwerg.com
*/
// Crea o refresh albero
function designTree() {
	$("#tree").Treeview({ 
		control: false ,
		speed: 'fast',
		collapsed:false
	});
				
	$("li/span", "#tree").each(function(i){
		// Preleva l'ID della sezione
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
	
		// Crea il tag per il form
		$(this).html('<label for="'+id+'">'+$(this).html()+'</label>') ;
	});
}