<script type="text/javascript">
var ajaxContentsUrl = "{$html->url('/areas/listContentAjax')}";
var ajaxSectionUrl = "{$html->url('/areas/listSectionAjax')}";
<!--
{literal}
$(document).ready(function() {
	// unbind default behavior
	$(".publishingtree LI").unbind("click");
	
	$(".publishingtree LI").click(function() {
	
		rel = $(this).attr("rel").split(":");
		urlC = ajaxContentsUrl + "/" + rel[1];
		urlS = ajaxSectionUrl + "/" + rel[1];
		
		$("#loading").show();
		
		$("#areacontentC").load(urlC, function() {
			$("#areacontent").sortable ({
				distance: 20,
				opacity:0.7//,
				//update: $(this).reorderListItem
			}).css("cursor","move");
			
			$("#areasectionsC").load(urlS, function() {
				$("#areasections").sortable ({
					distance: 20,
					opacity:0.7//,
					//update: $(this).reorderListItem
				}).css("cursor","move");
				$("#loading").hide();
			}); 
		});
		
		$(".publishingtree LI").removeClass("on");
		$(this).addClass("on");
		$("#sectionTitle").text($(this).text());
		$(".tab").next().show();
		$(".tab h2").css("background-position","right -25px");
		
	});
	
});
{/literal}
//-->
</script>


<form id="frmTree" method="post" action="{$html->url('/areas/saveTree')}">
	<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}"/>
	<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}"/>
	<input type="hidden" id="data_tree" name="data[tree]" 			value=""/>



	<div class="publishingtree" style="width:295px; margin-left:20px;">
			
		{$beTree->view($tree)}
		
	</div>



</form>



