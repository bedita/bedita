/**
*	areas custom js
*
*	
*	TO DEFINE in view:
*	ajaxContentsUrl = url to ajax calls for load content for a section
*	ajaxSectionsUrl = url to ajax calls for load children sections
*	ajaxSectionObjectUrl = url to ajax calls for load a section object
*/

var ajaxContentsUrl = "/areas/listContentAjax";
var ajaxSectionsUrl = "/areas/listSectionAjax";
var ajaxSectionObjectUrl = "/areas/loadSectionAjax";

var ajaxAreaObjectUrl = "/areas/loadAreaAjax";


$(document).ready(function() {

/*...........................................    

   load  areas (publishing)

...........................................*/	

// unbind default behavior on tree
$(".publishingtree H2").unbind("click");
	
$(".publishingtree H2").click(function() {
	
		rel = $(this).attr("rel").split(":");
		urlC = ajaxContentsUrl + "/" + rel[1];
		urlS = ajaxSectionsUrl + "/" + rel[1];
		urlSO = ajaxAreaObjectUrl + "/" + rel[1];
		
		//$(this).parent("div").find("ul").toggle();
		//$(this).parent("div").find(".plusminus").toggleClass("on");
			
		$("#loading").show();
		
	// load area
		$("#areapropertiesC").load(urlSO, function() {
			
			// restore tab behavior for section detail tabs (permission, custom properties)
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
			
			// load contents 
			$("#areacontentC").load(urlC, function() {
				
				// load children sections
				$("#areasectionsC").load(urlS, function() {
					
					$("#loading").hide();
					
				}); 
			});
		});
		
		$(".publishingtree H2").removeClass("on");
		$(".publishingtree LI").removeClass("on");
		$(this).addClass("on");
		$("#sectionTitle").text($(this).text());
		
		// open tab if it's not opened
		if ( $(".tab:first").next().css("display") == "none" ) {
			$(".tab:first").click();
		}
});


/*...........................................    

   load sections

...........................................*/	

	// unbind default behavior on tree
	$(".publishingtree LI").unbind("click");
	
	// set on click behavior on tree sections
	$(".publishingtree LI").click(function() {
	
		rel = $(this).attr("rel").split(":");
		urlC = ajaxContentsUrl + "/" + rel[1];
		urlS = ajaxSectionsUrl + "/" + rel[1];
		urlSO = ajaxSectionObjectUrl + "/" + rel[1];
		
		$("#loading").show();
		
		// load section
		$("#areapropertiesC").load(urlSO, function() {
			
			// restore tab behavior for section detail tabs (permission, custom properties)
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
			
			// load contents 
			$("#areacontentC").load(urlC, function() {
				
				// load children sections
				$("#areasectionsC").load(urlS, function() {
					
					$("#loading").hide();
					
				}); 
			});
		});
		
		$(".publishingtree H2").removeClass("on");
		$(".publishingtree LI").removeClass("on");
		$(this).addClass("on");
		$("#sectionTitle").text($(this).text());
		
		// open tab if it's not opened
		if ( $(".tab:first").next().css("display") == "none" ) {
			$(".tab:first").click();
		}
		
	});


	
});