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

	//$(".tab + *").hide();


	$(".tab").toggle(

		  function () {

			$(this).next().toggle() 		

			//$("h2",this).css("background-position","right -25px");
			
			$("h2",this).toggleClass("open");
		  },

		  function () {

			$(this).next().toggle() 		

			//$("h2",this).css("background-position","right 0px");
			$("h2",this).toggleClass("open");

		  }

		);



	$(".aprichiudi").toggle(

		  function () {

			$(".tab h2").css("background-position","right -25px");

			$(".tab").next().show();

			$(".aprichiudi").text('chiudi tutti');

		  },

		  function () {

			$(".tab h2").css("background-position","right 0px");

			$(".tab").next().hide();

			$(".aprichiudi").text('apri tutti');

		  }

		);



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
				//window.location = $(this).attr("rel");
			}
			$(this).parent("div").find("ul").toggle();
			$(this).parent("div").find(".plusminus").toggleClass("on");
			
		
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

	

	$(".multimediaitem").mouseover(function () {

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
		
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
		var destination = $(this).attr("rel");
		
		var myTop = $(this).position().top;
		//alert(myTop);
		
		$("#modaloverlay").show().width(w).height(h).click(function () {
			$(this).hide();
			$("#modal").hide();
		});
		
		//$("#modal").toggle().css("top",myTop);
		$("#modal").toggle();

		if ($(this).attr("rel")) {
			$("#modalmain").empty().addClass("loader").load(destination).ajaxStop(function(){
				$(this).removeClass("loader")
			});
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


});



