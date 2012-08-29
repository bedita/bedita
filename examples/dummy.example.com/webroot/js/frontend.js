
/*...........................................    

   gianni box

...........................................*/



jQuery.fn.gianniBox = function(modclass) {
	
	if (modclass == "multimedia") {
		var url = "/pages/getMedia/" + $(this).attr("rel");
		
		var type = $(this).attr("alt");
		
	} else if (modclass == "mymailto") {
		var url = "/pages/mymailto/" + $(this).attr("rel");
		
	} else {
		var url =  $(this).attr("rel");
	}
	
	
	if (typeof(url) != "undefined") {	
		if (!$('.modal').is(":visible")) {
			//alert(url);
			$("BODY").append("<div class='overlay' style='margin-top:-161px;'></div><div style='margin-top:160px;' id="+type+" class='modal "+modclass+"'><div class='modalcontent'></div></div>");
			var heightM = $(document).height();
			$(".overlay").height(heightM);
			
			/*
			$(".modalclose, .overlay").click(function(){
				$(".flashcontent").css({'display':'block'});
				$(".modal, .overlay").remove();
				document.location.hash="";
			});
			*/

		} 
	}	
	
	$(".overlay").fadeTo(0.5, 0.8);
	$(".modalcontent").html("").css({
		background: "#FFF url('/img/loading.gif') center 135px no-repeat"
	})
	.load(url)
	.ajaxComplete(function(){
		$(this).css("background-image","none");
		$(".flashcontent").css({'display':'none'});
		var heightM = $(document).height();
		$(".overlay").height(heightM);
		$(".modalclose, .overlay").click(function(){
			
			$(".flashcontent").css({'display':'block'});
			$(".modal, .overlay").remove();
			document.location.hash="";
		});
		
	});

}

jQuery.fn.gianniBoxModal = function(modclass) {
	
	if (modclass == "multimedia") {
		var url = "/pages/getMedia/" + $(this).attr("rel");
		
		var type = $(this).attr("alt");
		
	} else if (modclass == "mymailto") {
		var url = "/pages/mymailto/" + $(this).attr("rel");
		
	} else {
		var url =  $(this).attr("rel");
	}
	
	
	if (typeof(url) != "undefined") {	
		if (!$('.modal').is(":visible")) {
			//alert(url);
			$("BODY").append("<div class='overlay'></div><div id="+type+" class='modal "+modclass+"'><div class='modalcontent'></div></div>");
			var heightM = $(document).height();
			$(".overlay").height(heightM);
			
			/*
			$(".modalclose, .overlay").click(function(){
				$(".flashcontent").css({'display':'block'});
				$(".modal, .overlay").remove();
				document.location.hash="";
			});
		*/

		} 
	}	
	
	$(".overlay").fadeTo(0.5, 0.8);
	$(".modalcontent").html("").css({
		background: "#FFF url('/img/loading.gif') center 135px no-repeat"
	})
	.load(url)
	.ajaxComplete(function(){
		$(this).css("background-image","none");
		$(".flashcontent").css({'display':'none'});
		var heightM = $(document).height();
		$(".overlay").height(heightM);
		$(".modalclose, .overlay").click(function(){
			
			$(".flashcontent").css({'display':'block'});
			$(".modal, .overlay").remove();
			document.location.hash="";
		});
		
	});
		

}

/*...........................................    

   General functions

...........................................*/	

$(document).ready(function(){
	
/*...........................................    

   gianni box

...........................................*/


$(".gbox").click(function(){
		$(this).gianniBox("multimedia");

});

$(".gboxM").click(function(){
		
		
		$(this).gianniBoxModal("multimedia");
		
		var position = $(this).position();
		var positionTop = ((position.top)+10)+"px";
		$(".modal").css({'margin-top':''+positionTop+''});
		

});




	
});
