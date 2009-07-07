	
	
	{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			
			$(".icons LI").css("cursor","pointer").mouseover(function() {
				var myclass = $(this).attr("rel");
				$(".secondacolonna .modules label").removeClass().addClass("{/literal}{$moduleName}{literal}").addClass(""+myclass+"");
			});
		});
	</script>
	{/literal}
	
	<a href="javascript:void(0)" onClick="$('.test').toggle()">test</a>
	</div>
	
	
	
	<div class="test" style="display:none">
		
		<ul class="icons">
			<li>ecco le varie icone di stato:</li>
			<li rel="readonly">Readonly</li>
			<li rel="fixedobject">Fixed</li>
			<li rel="lock">Locked</li>
			<li rel="future">Future</li>
			<li rel="trashed">Trashed</li>
			<li rel="concurrentuser">Concurrentuser</li>
			<li rel="alert">Alert</li>
			<li rel="error">Error</li>
			<li rel="pending">Pending</li>
			<li rel="unsent">Unsent</li>
			<li rel="save">Save</li>
		</ul>
		
		<input type="button" title="Io sono un test per la modale" rel="{$html->webroot}testmodal.html'" class="modalbutton" value="modal test example" />

	</div>