/**
 * BeditaUI - javascript UI functions and methods 
 * @param {Object} ".modules *[rel]"
 */


/*...........................................    

   General functions

...........................................*/


/*
* Date - force two date pickers to work as a date range 
*/

function customRange(input)
{
    return {minDate: (input.id == 'end' ? $('#start').datepicker( "getDate" ) : null), 
        	maxDate: (input.id == 'start' ? $('#end').datepicker( "getDate" )  : null)}; 
}


/*
 * Extend JQuery
 */
 
 /*...........................................    

   Base addon functions

...........................................*/	


jQuery.fn.toggleText = function(a, b) {
	return this.each(function() {
		jQuery(this).text(jQuery(this).text() == a ? b : a);
	});
};

jQuery.fn.toggleValue = function(a, b) {
	return this.each(function() {
		jQuery(this).val(jQuery(this).val() == a ? b : a);
	});
};



jQuery.fn.extend({
	check: function() {
		return this.each(function() { this.checked = true; });
	},
	uncheck: function() {
		return this.each(function() { this.checked = false; });
	},
	toggleCheck: function() {
		return this.each(function() { this.checked = !this.checked ; });
	},
	submitConfirm: function(params) {
		$(this).bind("click", function() {
			if(!confirm(params.message)) {
				return false ;
			}
			if (params.formId) {
				$("#" + params.formId).attr("action", params.action);
				$("#" + params.formId).submit(); 
			} else {
				$(this).parents("form").attr("action", params.action);
				$(this).parents("form").submit();
			}
		});
	},


/*
*	fixItemsPriority (was reorderListItems)
*	optional first parameter define priority start number
*/
	fixItemsPriority: function ()
	{
		if(window.priorityOrder === undefined) {
			priorityOrder = "asc";
		}
					
		if(priorityOrder == "desc") {

			priority = parseInt( (arguments.length > 0 && typeof(arguments[0]) != 'object' && typeof(arguments[0]) != 'undefined')? arguments[0] : $(this).find("input[name*='[priority]']:first").val() );
			
			$(this).find("input[name*='[priority]']:enabled").each(function(index)
			{
				$(this).val(priority--)								// update priority
				.hide().fadeIn(100).fadeOut(100).fadeIn('fast');
			});
			
		} else {
			priority = parseInt( (arguments.length > 0 && typeof(arguments[0]) != 'object' && typeof(arguments[0]) != 'undefined')? arguments[0] : 1 );
			
			$(this).find("input[name*='[priority]']:enabled").each(function(index)
			{
				$(this).val(priority++)								// update priority
				.hide().fadeIn(100).fadeOut(100).fadeIn('fast');	// pulse effect
			});
		}
	}


});



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
		if ($(this).attr("rel")) {
			window.location = ($(this).attr("rel"));
		}
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
			
		$(this).next().show('fast') ;	
		$("h2",this).addClass("open").addClass(currentclassmodule);
	
	};

	jQuery.fn.BEtabsclose = function() {
			
		$(this).next().hide('fast') ;	
		$("h2",this).removeClass("open").removeClass(currentclassmodule);
	
	};
	
	
	
	$(".tab").click(function (){
		
		$(this).BEtabstoggle() ;	

	});





/*...........................................    

   TAB orizzontali

...........................................*/
	
	$(".htabcontainer .htabcontent:first-child").show();
	$(".htab TD:first-child,.htab LI:first-child").addClass("on");
	
	$(".htab TD,.htab LI").click(function() {

		var trigged 		  = $(this).attr("rel");
		var containermenu 	  = $(this).parents(".htab");
		var containercontents = $("#"+trigged+"").parent().attr("id");

		$("#"+containercontents+" .htabcontent").hide();
		$("#"+trigged+"").show();

		$("TD,LI",containermenu).removeClass("on");
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

	$(".indexlist TR[rel]").click(function() {
		
		window.location = ($(this).attr("rel"));
	});

/*
	$(".indexlist THEAD TH").click(function() {
		
		$(this).toggleClass("on");	
		
	});
*/

/*...........................................    

   publishing tree

...........................................*/	



	$(".publishingtree DIV UL").hide();

	$(".publishingtree h2").before("<div class='plusminus'></div>");

	
	$(".publishingtree .plusminus").click(function () {
			$(this).toggleClass("on").parent("div").find("ul").toggle();
	});



	$(".publishingtree h2 A").click(function () {
		
			if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
			}
			
		
	});

	$(".publishingtree LI A").click(function () {

			if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
			}

	});
	
	$(".publishingtree LI A.on").parents("DIV:first").find("UL").show();
	

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
		
		$(".vlist, .imagebox, .info_file_item").toggle();
		
		$('.multimediaitem').css("float","none");
		
		$('.vlist').css("white-space","nowrap");
		
		//alert(1);

		
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

	$("#mediatypes LI").click(function () {
	
		$("#mediatypes LI").removeClass("on");
		$(this).addClass("on");
		var valore = $("input", this).attr("value");
		$("#mediatypes input").val([valore]);
		if ($(this).attr("rel")) {
				window.location = $(this).attr("rel");
		}
	});

	$("#mediatypes LI.ico_all").click(function () {
		$("#mediatypes LI").addClass("on");
	});




/*...........................................    

   modal

...........................................*/

jQuery.fn.BEmodal = function(){

	$("#modal").draggable({
		handle : "#modalheader"
	});

	var w = window.innerWidth || self.innerWidth || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || document.body.clientHeight;
	
	var h = 1500; 
	//alert(h);
	
	var destination = $(this).attr("rel");
	var title = $(this).attr("title");
	
	//var myTop = $(this).position().top;
	var myTop = $(".secondacolonna").position().top;

	
	$("#modaloverlay").show().fadeTo("fast", 0.8).width(w).height(h).click(function () {
		//$(this).hide();
		//$("#modal").hide();
	});
	
	$("#modal").toggle().css("top",myTop);

	if ($(this).attr("rel")) {
		$("#modalmain").empty().addClass("modaloader").load(destination).ajaxStop(function(){
			$(this).removeClass("modaloader")
		});
	};

	
	if ($(this).attr("title")) {
		$("#modalheader .caption").html(title+"&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;");
	};
	
	
	$("#modalheader .close").click(function () {
		$("#modal").hide();
		$("#modaloverlay").hide();
	});
/*
	$("#modalheader .full").click(function () {

	});
*/


}




	$(".modalbutton").click(function () {
	
		$(this).BEmodal();

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
						


/*...........................................    

   bulk actions

...........................................*/


	$('.selecteditems').text($(".objectCheck:checked").length);
	$(".selectAll").bind("click", function(e) {
		var status = this.checked;
		$(".objectCheck").each(function() { this.checked = status; });
		$('.selecteditems').text($(".objectCheck:checked").length);
	}) ;
	$(".objectCheck").bind("click", function(e) {
		var status = true;
		$(".objectCheck").each(function() { if (!this.checked) return status = false;});
		$(".selectAll").each(function() { this.checked = status;});
		$('.selecteditems').text($(".objectCheck:checked").length);
	}) ;

/*...........................................    

   help

...........................................*/



/*...........................................    

   editor notes

...........................................*/

	$('.editorheader').css("cursor","pointer").click(function () {
		$(this).next().slideToggle('fast');
	});




});

/*...........................................    

   keyboard binding

...........................................*/

document.onkeydown = function(e){ 	
	if (e == null) { // ie
		keycode = event.keyCode;
	} else { // mozilla
		keycode = e.which;
	}
	
	if(keycode == 27){ // 
		
		if ($('.tab').next().is(":visible")) {
			$('.tab').BEtabsclose();
		} else {
			$('.tab').BEtabsopen();
		}
		
	} else if(keycode == 109){ // 
		
		//$('.tab').BEtabsopen();
		//helptrigger
		
	} else if(keycode == 122){ // 
		
		//$('.helptrigger').click();
	
	
	} else if(keycode == 188){ // 
		
	}
	//alert(keycode);
};


/*...........................................    

   openAtStart

...........................................*/

function openAtStart(defaultOpen) {

	var cookieTitle = document.title;
	var openAtStart = $.cookie(cookieTitle);
	if (openAtStart == null) {
		var openAtStart = defaultOpen;
	}
	$(openAtStart).prev(".tab").BEtabstoggle();
	
	$(window).unload(function(){
		openAtStart = new Array();
		$(".tab").each(function(i){
			if ($(this).next().is(":visible")) {
				openAtStart.push("#" + $(this).next().attr("id"));
			}
		});
		$.cookie(cookieTitle, openAtStart);
	});
}


	