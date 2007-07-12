/*
File con la logica utilizzata nel modulo aree.

@author		giangi@qwerg.com
*/

/* ******************************
treeView Aree
****************************** */
	// Crea o refresh albero
	function designTree() {
		$("#tree").Treeview({ 
			control: "#treecontrol" ,
			speed: 'fast',
			collapsed:false
		});
		
		// Inserisce l'URL per la modifica dell'area o della sezione
		var URLFrmArea 		= "/" ;
		var URLFrmSection 	= "/" ;
		
		try {
			URLFrmArea 		= $("input[@name='URLFrmArea']").eq(0).attr('value') ;
			URLFrmSection 	= $("input[@name='URLFrmSezione']").eq(0).attr('value') ;
		} catch(e) {}
		
		$("li/span[@class='SectionItem']", "#tree").each(function(i){
			// Preleva l'ID della sezione
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			
			// Crea il tag per il form
			$(this).html('<a href="'+URLFrmSection+id+'">'+$(this).html()+'</a>') ;
		});
		
		$("li/span[@class='AreaItem']", "#tree").each(function(i){
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			
			$(this).html('<a href="'+URLFrmArea+id+'">'+$(this).html()+'</a>') ;
		});

	}
	
	// Resetta l'albero per ridisegnarlo
	function resetTree() {
		$('div', "#tree").remove();
		$('span', "#tree").removeAttr('style');
		$('ul', "#tree").removeAttr('style');

		$("li[@class='expandable lastExpandable'], li[@class='expandable']", "#tree").attr('class', 'closed');
		$("li[@class='collapsable lastCollapsable'], li[@class='last'], , li[@class='closed last']", "#tree").removeAttr('class');
				
		$('li/ul', "#tree").each(function(i){
			if(!($('li', this).size())) {
				$(this).remove() ;
			}
		});
	}

	// Aggiunge i comandi allbero, nascosti
	function addCommand() {
		$("li/span[@class='SectionItem']", "#tree").before('<input type="button" name="up" value=" up " class="initCmdTree">&nbsp;<input type="button" name="down" value=" down " class="initCmdTree">&nbsp;&nbsp;');
	}
	
	// Rende visibili i comandi accessibili e disabilita gli altri
	function refreshCommand() {
		$("li", "#tree").each(function(i){

			if($(this).siblings(this).size() == 0) {
				// figlio unico
				$('input', this).attr({style: 'block', class: 'disabledCmdTree'}) ;
				return ;
				
			} 

			$("input", this).attr({style: 'block', class: 'enabledCmdTree'}) ;
			
			if($(this).prev(this).size() == 0) {
				// e' il primo
				$("input[@name='up']", this).attr({style: 'block', class: 'disabledCmdTree'}) ;
				
			} else if($(this).next(this).size() == 0) {
				// e' l'ultimo
				$("input[@name='down']", this).attr({style: 'block', class: 'disabledCmdTree'}) ;
				
			}			

		});
				
	}

	// Assegna i comandi ai button
	function refreshOnClick() {
		$("input[@name='up'][@class='enabledCmdTree']", "#tree").click(function(i) { 			
			$(this.parentNode).prev().before(this.parentNode);

			$("input[@name='up'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			$("input[@name='down'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			
			resetTree() ;
			designTree() ;
			refreshCommand() ;
			refreshOnClick();
		} );		

		$("input[@name='down'][@class='enabledCmdTree']", "#tree").click(function(i) { 
			$(this.parentNode).next().after(this.parentNode);
					
			$("input[@name='up'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			$("input[@name='down'][@class='enabledCmdTree']", "#tree").unbind('click') ;

			resetTree() ;
			designTree() ;
			refreshCommand() ;
		} );		
	}
	

/* ******************************
****************************** */
