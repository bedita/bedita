jQuery.fn.toggleText = function(a, b) {
	return this.each(function() {
		jQuery(this).text(jQuery(this).text() == a ? b : a);
	});
};


$(document).ready(function(){

/*
	$(".headmenu LI").mouseover(function(){
		$(this).addClass("on");
		
	});
*/
 	$(document).pngFix(); 

	
	$(".menuP LI").hover(
	function(){
		/*var currentmenu = $(this).attr("title");
		var p = $("."+currentmenu);
		var position = p.position();
		var leftpos = position.left+'px';
		
		$("UL.subsection").css({'margin-left':leftpos});
		$("#"+currentmenu).show("fast");*/
		
		$(".subsection", this).show();
		
	},
	
	function () {
		$(".subsection", this).hide();
	});	
	
	
	$(".XXXthumb A").click(function(){
		
		var heightM = $(document).height();
		var widthM = $(document).width();
		$(".giannicontainer").show();		
		$(".overlay").show().height(heightM).fadeTo(0.5, 0.5);
		var imagesource 		= $(this).attr("rel");
		var imagetitle 			= $(this).attr("title");
		var imagedescription 	= $(this).parent().find(".thumbdescription").text();
		
		$(".bigImg").attr({src:imagesource, alt:imagetitle, width:680});
		$(".bigImgTitle").text(imagetitle);
		$(".bigImgDescription").text(imagedescription);
		
	});

	
 	$(".close").click(function () {
		$(".overlay").hide();
		$(".giannicontainer").hide();
		$("object").show();
	});
	
	
/*...........................................    

   Staging

...........................................*/	
	
	$(".stagingmenu LI A").click(function(){
		var myLeft = $(this).position().left;
		var trigged  = $(this).attr("rel");
		$("#"+trigged+"").css("left",myLeft).toggle('fast');

	
	});

	$(".openclose").click(function(){
		$(".stagingsubmenu").hide();
		$(".stagingmenu LI.in").toggle('fast');
		$(this).toggleText("›","‹");
	});

	
	
	
	
});



