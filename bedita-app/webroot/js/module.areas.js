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

$(document).ready(function() {

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
		
		
		$(".publishingtree LI").removeClass("on");
		$(this).addClass("on");
		$("#sectionTitle").text($(this).text());
		
		// open tab if it's not opened
		if ( $(".tab:first").next().css("display") == "none" ) {
			$(".tab:first").click();
		}
		
	});
	
	
});