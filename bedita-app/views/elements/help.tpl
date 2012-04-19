{*
** Help container
** Included in layout/default.tpl
*}

{assign var='params' value=$html->params}
<script type="text/javascript">
var remote_url_response = "{$html->url('/pages/helpOnline/')}{$params.controller}/{$params.action}";
$().ready(function(e){
	$('.helptrigger').click(function () {

		if (!$("#helpcontent").length) {		
			//fa la chiamata ajax solo se non già fatta precedentemente
			$("#helpcontainer2").addClass("loadingHelp");
			$("#helpcontainer2").append("<div id='helpcontent'></div>");
			
			$.get(remote_url_response, function(html){
				$(html).find(".textC").appendTo("#helpcontent");
				$("#helpcontainer2").removeClass("loadingHelp");
			});		
		} 

		$('#helpcontainer, .quartacolonna, .main, .mainhalf, .mainfull, .insidecol').toggle();
		$(this).toggleClass("helpon");
	});
});
</script>

<div id="helpcontainer">
	<div id="helpcontainer2" class="graced">
		<h2 class="bedita">
			BEhelp } {$currentModule.label|default:''} } {$html->action}
		</h2>
		<hr />
	</div>
</div>