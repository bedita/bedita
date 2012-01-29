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
	},

	triggerMessage: function(type, pause) {
		var $_this = $(this);
		if (pause == undefined) {
			pause = 4;
		}
		if (type == "error") {
			$_this.show();
		} else if (type == "info") {
			$_this
				.show()
				.animate({opacity: 1.0}, pause)
				.fadeOut(1000);
		} else if (type == "warn") {
			$_this
				.show();
		}
			
		$_this.find(".closemessage").click(function() {
			$_this.fadeOut('slow');
		});
		
		$_this.find(".messagedetail").click(function() {
			$(this).next().toggle();
		});
	}


});



$(document).ready(function(){


/*...........................................    

   home

...........................................*/

	$(".pub H2",".hometree").wrap("<div class='tab' />"); 

	$("LI A[rel]",".hometree").css("cursor","pointer").click(function () {
		window.location = $(this).attr("rel");
	});
	
	//$(".publishingtree LI A.on",".home").parents("DIV:first").find("UL").show();
	
/*...........................................    

   moduloni

...........................................*/	
	
	$(".modules *[rel]").css("cursor","pointer").mouseover(function () {

			$(this).css("background-repeat","repeat");

		}).mouseout(function(){

			$(this).css("background-repeat","repeat-x");

		}).click(function () {
			
			if ($(this).hasClass("bedita")) {
				//
			} else {
				window.location = $(this).attr("rel");
			}
	});

/*...........................................    

   menu TOP moduli

...........................................*/

	$(".modulesmenu a").mouseover(function (e) {		

		var rightpos = e.clientX;	

		$(this).addClass("over");

		$(".modulesmenucaption").toggle().css("left",rightpos - 100);

		$(".modulesmenucaption A").text($(this).attr("title"));

	}).mouseout(function(){

		$(this).removeClass("over");

		$(".modulesmenucaption").toggle();

		$(".modulesmenucaption A").text("be");		

	}).click(function () {
		if ($(this).attr("href")) {
			window.location = ($(this).attr("href"));
		}
	});
		

/*...........................................    

   gray TABs

...........................................*/

	var currentclassmodule   = $(".secondacolonna .modules LABEL").attr("class");
	if(!currentclassmodule) {
		currentclassmodule = "";
	}

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
	
	$(".tab:not(.stayopen)").click(function (){
		
		$(this).BEtabstoggle() ;	

	});

	
	$(".tab.stayopen H2").addClass("open").addClass(currentclassmodule);

	
/*...........................................    

   horizontal TABs

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



	$(".indexlist TR:has(input:checked)").addClass("overChecked");
	
	$(".indexlist input.objectCheck").change(function(){

		$(this).parents("TR").toggleClass("overChecked");

	});


	
	$(".indexlist TR[rel]").not('.idtrigger').click(function() {
		
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

	//$(".publishingtree DIV UL").hide();

	$(".publishingtree h2").before("<div class='plusminus'></div>");

	
	$(".publishingtree .plusminus").click(function () {
			$(this).toggleClass("on").parent("div").find("ul").toggle('slow');
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
	
	var myTop = $(window).scrollTop() + 20;
	
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

		$(".BEbutton .golink").click(function () {
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
		$(".objectCheck").each(function() { 
			this.checked = status; 
			if (this.checked) $(this).parents('TR').addClass('overChecked');
			else $(this).parents('TR').removeClass('overChecked');
		});
		$('.selecteditems').text($(".objectCheck:checked").length);
	}) ;
	$(".objectCheck").bind("click", function(e) {
		var status = true;
		$(".objectCheck").each(function() { 
			if (!this.checked) return status = false;
		});
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


/*...........................................    

   accessories

...........................................*/

$(".idtrigger").css("cursor","pointer").click(function() {
	var trigged  = $(this).attr("rel");
	$("#"+trigged+"").toggle();
});

/*...........................................    

   search

...........................................*/

$(".searchtrigger").click(function() {
	$(".searchobjects").toggle();
});


/*...........................................    

   versin / history

...........................................*/


/*...........................................    

  modulelist menu

...........................................*/


$(".primacolonna .modules label.bedita").toggle(function() {
	$(this).addClass("shadow");  
	$(".modulesmenu_d").show();

}, function() {

  $(this).removeClass("shadow");  
	$(".modulesmenu_d").hide();
});

$(".modulesmenu_d LI[class]").each(function() {
	var classname = $(this).attr('class');
	var color = $(".modulesmenu ."+classname).css('background-color');
	$(this).css("border-color", color);
});

$(".modulesmenu_d LI[class]").hover(
  function () {
  	var classname = $(this).attr("class");
	var position = $(this).position();
	var topshift = position.top;
	var leftshift = $(".modulesmenu_d").width();
	$(".sub_modulesmenu_d."+classname+"").css({
		"left": leftshift+160+"px",
		"top":  topshift+20+"px"
	}).show();
	
  }, 
  function () {
	$(".sub_modulesmenu_d").hide();
  }
);


/*...........................................    

   statusInfo

...........................................*/

	//var objstatus = $(".secondacolonna .modules label").attr("class");




});

/* end of document ready() */


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

		if ($('.tab2').next().is(":visible")) {
			$('.tab2').BEtabsclose();
		} else {
			$('.tab2').BEtabsopen();
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

/*...........................................    

   utility

...........................................*/
	  
	  
function getFlashVersion(){ 
	// ie 
	try { 
		try { 
			// avoid fp6 minor version lookup issues 
			// see: http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/ 
			var axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6'); 
      		try { 
				axo.AllowScriptAccess = 'always'; 
			} catch(e) {
				return '6,0,0';
			} 
    	} catch(e) {} 
    	return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g, ',').match(/^,?(.+),?$/)[1]; 
	// other browsers 
	} catch(e) { 
		try { 
			if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin){ 
				return (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1]; 
			} 
		} catch(e) {} 
	} 

  	return false; 
} 

