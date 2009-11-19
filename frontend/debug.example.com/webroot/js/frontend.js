/*...........................................    

   General functions

...........................................*/	

$(document).ready(function(){
	
	$(".menuleft UL LI A").click(function () {
     $(this).next("UL").slideToggle('slow');

    });

	
});


