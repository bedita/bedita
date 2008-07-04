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

			$("h2",this).css("background-position","right -25px");

		  },

		  function () {

			$(this).next().toggle() 		

			$("h2",this).css("background-position","right 0px");

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
	
	$(".htabcontent:first").show();
	$(".htab LI:first").addClass("on");
	
	$(".htab LI").click(function() {

		var trigged = $(this).attr("rel");		
		$(".htabcontent").hide();
		$("#"+trigged+"").toggle();
		$(".htab LI").removeClass("on");
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


	$(".publishingtree h2").click(function () {
	
			$(this).parent("div").find("ul").toggle();

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

		// $(this).addClass("dark");

	}).mouseout(function () {

		// 	$(this).removeClass("dark");

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
	
	
		/*
		else if (cosa = "viewsmall") $(".multimediaitem").addClass("small");	
		else if (cosa = "viewthumb") $(".multimediaitem").removeClass("small");
		*/







});



