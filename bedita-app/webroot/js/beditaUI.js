/**
 * BeditaUI - javascript UI functions and methods 
 * @param {Object} ".modules *[rel]"
 */


$(document).ready(function(){



/*...........................................    

   moduloni

...........................................*/

	$(".modules *[rel]").css("cursor","pointer");

	$(".modules *").mouseover(function () {

			$(this).css("background-repeat","repeat");


		}).mouseout(function(){

			$(this).css("background-repeat","repeat-x");
		

		}).click(function () {

			if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
			}

	});



/*...........................................    

   menu TOP moduli

...........................................*/



	$(".modulesmenu li").mouseover(function (e) {		

		var rightpos = e.clientX;	

		$(this).addClass("over");

		$(".modulesmenucaption").toggle().css("left",rightpos - 100);

		$(".modulesmenucaption A").text($(this).attr("title"));

	}).mouseout(function(){

		$(this).removeClass("over");

		$(".modulesmenucaption").toggle();

		$(".modulesmenucaption A").text("be");

		

	}).click(function () {

			window.location = ($(this).attr("href"));

	});

		

/*...........................................    

   TAB grigi

...........................................*/

	var currentclassmodule   = $(".secondacolonna .modules LABEL").attr("class");

	jQuery.fn.BEtabstoggle = function() {
			
		$(this).next().toggle('fast') ;	
		$("h2",this).toggleClass("open").toggleClass(currentclassmodule);
	
	};

	jQuery.fn.BEtabsopen = function() {
			
		$(this).next().show() ;	
		$("h2",this).addClass("open").addClass(currentclassmodule);
	
	};

	jQuery.fn.BEtabsclose = function() {
			
		$(this).next().hide() ;	
		$("h2",this).removeClass("open").removeClass(currentclassmodule);
	
	};
	
	
	
	$(".tab").click(function (){
		
		$(this).BEtabstoggle() ;	

	});





/*...........................................    

   TAB orizzontali

...........................................*/
	
	$(".htabcontainer .htabcontent:first-child").show();
	$(".htab LI:first-child").addClass("on");
	
	$(".htab LI").click(function() {

		var trigged 		  = $(this).attr("rel");
		var containermenu 	  = $(this).parents("UL");
		var containercontents = $("#"+trigged+"").parent().attr("id");

		$("#"+containercontents+" .htabcontent").hide();
		$("#"+trigged+"").show();

		$("LI",containermenu).removeClass("on");
		$(this).addClass("on");
	
	  });




/*...........................................    

   tabelle e stati dei TR e click

...........................................*/


	$(".indexlist TR").mouseover(function() {

		$("TD",this).addClass("over");

	}).mouseout(function(){

		$("TD",this).removeClass("over");	

	});

	$(".indexlist TR.rowList").click(function() {
		
		window.location = ($(this).attr("rel"));
	});



/*...........................................    

   publishing tree

...........................................*/	



	$(".publishingtree DIV UL").hide();

	$(".publishingtree h2").before("<div class='plusminus'></div>");

	
	$(".publishingtree .plusminus").click(function () {
			$(this).toggleClass("on").parent("div").find("ul").toggle();
	});



	$(".publishingtree h2").click(function () {
		
			if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
			}
			
		
	});

	$(".publishingtree LI").click(function () {

			if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
			}

	});
	
	$(".publishingtree LI.on").parents("DIV:first").find("UL").show();
	

/*...........................................    

   multimediaitem

...........................................*/	

	

	$("#viewthumb .multimediaitem").mouseover(function () {

	 	$(this).toggleClass("dark");

	}).mouseout(function () {
		
		$(this).toggleClass("dark");

	});


/*...........................................    

   multimediaitem icon toolbar

...........................................*/



	$(".multimediaitemToolbar.viewlist").click(function () {
		
		$("#viewlist").toggle();
		$("#viewthumb").toggle();
	});
	
	$(".multimediaitemToolbar.viewsmall").click(function () {
		$("#viewlist").hide();
		$("#viewthumb").show();
		$(".multimediaitem").addClass("small");
	});

	$(".multimediaitemToolbar.viewthumb").click(function () {
		$("#viewlist").hide();
		$("#viewthumb").show();
		$(".multimediaitem").removeClass("small");
	});
	

/*...........................................    

   modal

...........................................*/

	
	$(".modalbutton").click(function () {
	
		$("#modal").draggable();
			 	
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
		var destination = $(this).attr("rel");
		var title = $(this).attr("title");
		
		var myTop = $(this).position().top;
		//alert(myTop);
		
		$("#modaloverlay").show().width(w).height(h).click(function () {
			$(this).hide();
			$("#modal").hide();
		});
		
		$("#modal").toggle().css("top",myTop);
		//$("#modal").toggle();

		if ($(this).attr("rel")) {
			$("#modalmain").empty().addClass("loader").load(destination).ajaxStop(function(){
				$(this).removeClass("loader")
			});
		};

		
		if ($(this).attr("title")) {
			$("#modalheader .caption").html(title+"&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;");
		};
		
		
		$("#modalheader .close").click(function () {
			$("#modal").hide();
			$("#modaloverlay").hide();
		});

	});





/*...........................................    

   bottoni

...........................................*/

		$(".BEbutton .link").click(function () {
			if ($(this).attr("href")) {
				window.open($(this).attr("href"));
			}
		});


/*...........................................    

   autogrow

...........................................*/
	
	$('.autogrowarea').autogrow({
		lineHeight: 16
	});
						





});



