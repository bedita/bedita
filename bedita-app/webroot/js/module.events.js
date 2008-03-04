// Crea o refresh albero
function designTree() {
	$("#tree").Treeview({
		control: false ,
		speed: 'fast',
		collapsed:false
	});

	var url = "" ;
	try {
		url = URLBase ;
	} catch(e) {
		url = "/events/index/" ;
	}

	$("li span", "#tree").each(function(i){
		// Preleva l'ID della sezione
		var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;

		// Crea il tag per il form
		$(this).html('<a href="'+url+"id:"+id+'">'+$(this).html()+'</a>') ;
	});
}