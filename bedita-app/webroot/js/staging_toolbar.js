/**
 * Bedita staging toolbar - javascript functions and methods 
 */

$(document).ready(function(){
	
	// if jQuery.toggleText() is undefined we're going to define it
	if (!jQuery.isFunction(jQuery.fn.toggleText)) {
		
		jQuery.fn.toggleText = function(a, b) {
			return this.each(function() {
				jQuery(this).text(jQuery(this).text() == a ? b : a);
			});
		};
	
	}
	
	var barstatus =  $.cookie('BEdita_staging');
	$("#BEdita_staging_toolbar").attr('class',''+barstatus+'');

	//alert(barstatus);
	
	$(".stagingmenu LI A").click(function () {
	    var myLeft 	= $(this).position().left;
		var rel  	= $(this).attr("rel");
		var trigged  	= $("#"+rel+"");
		$(".stagingsubmenu").not(trigged).slideUp('normal');
		$(trigged).css("left",myLeft-60).slideToggle('normal');
	});

	
	$("#BEdita_staging_toolbar.close .stagingmenu LI.in").hide();
	$("#BEdita_staging_toolbar.close .openclose.arrow").text("›");
	
	$("#BEdita_staging_toolbar .openclose").click(function(){
		$("#BEdita_staging_toolbar").toggleClass('close');
		$(".stagingsubmenu").hide();
		$(".stagingmenu LI.in").toggle(800);
		$(".openclose.arrow").toggleText("›","‹");
		
		var barstatus = $("#BEdita_staging_toolbar").attr('class');
		var options = { path: '/', expires: 10 };
		$.cookie('BEdita_staging', barstatus, options);
	});


	$(".stagingsubmenu TR:has(TD A)").css("cursor","pointer").click(function(){
		//window.parent.location.href = $("TD A",this).attr("href");
	}).hover(
      function () {
        $(this).css("background-color","#666");
      }, 
      function () {
        $(this).css("background-color","transparent");
      }
    );
	
	$("#BEdita_staging_toolbar .grid").click(function(){
		$(this).toggleText("show grid","hide grid");
		$("BODY").toggleClass("gridy");
		
	});
	

	/*
	 * content editable
	 * 
	 * 
	*/
	/*
	$(".inlinemodify").click(function() {
		$(".contenteditable").attr("contenteditable","true").css("background-color","#ddd");
		$(".pagesubmit").parent().show();
		$(".pagecancel").parent().show();
		$(this).parent().hide();
	});

	$(".pagecancel").click(function() {
		$(".contenteditable").attr("contenteditable","false").css("background-color","#FFF");
		$(".pagesubmit").parent().hide();
		$(".inlinemodify").parent().show();
		$(this).parent().hide();
	});
	
	
	$(".pagesubmit").click(function() {
		var textosave = $(".contenteditable").html();	
		alert (textosave);
	});
	*/
});