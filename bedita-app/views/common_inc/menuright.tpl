{*
Template incluso.
Menu a DX
*}

{if !empty($object.note)}
<script type="text/javascript">
{literal}
	$(document).ready( function (){
		$("#editornotes").prev(".tab").BEtabstoggle();
	});
{/literal}
</script>
{/if}

<div class="quartacolonna">	

	
	<div class="tab"><h2>{t}Notes{/t}</h2></div>
			
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{strip}
		<label>editor notes:</label>
		<textarea name="data[note]" class="autogrowarea editornotes">
		  {$object.note|default:''}
		</textarea>
	{/strip}
		
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
	
	{* <a href="javascript:void(0)" onClick="$('.test').toggle()">test</a> *}
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
			
			
		<ul style="margin:10px 0px 10px 0px; border:1px solid gray; border-width:1px 0px 1px 0px">
			<li><a href="javascript:$('.main .tab').BEtabsopen();" class="openclose">open all TABS</a></li>
			<li><a href="javascript:$('.main .tab').BEtabsclose();" class="openclose">close all TABS</a></li>
		</ul>	
	
	</div>

</div>