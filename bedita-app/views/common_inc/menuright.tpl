{*
Template incluso.
Menu a DX
*}


<div class="quartacolonna">	

	<div class="tab"><h2>{t}Notes{/t}</h2></div>

				
	<div style="margin-top:-10px; padding:10px; background-color:white;">
{strip}
	<label>editor notes:</label>
	<textarea name="data[note]" class="autogrowarea editornotes">
	  {$object.note|default:''}
	</textarea>
{/strip}


{*
	
	<label>quick help:</label>

	<div class="help">
		
	</div>
	
*}	

	<hr />
	
	<ul>
		<li>icone da fare</li>
		<li>concurrent user*</li>
		<li>locked document*</li>
		<li>unsaved changes*</li>
		<li>trashed document*</li>
		<li>fixed document*</li>
		<li>user note*</li>
		<li>read only*</li>
		<li>error</li>
	</ul>
	
	
	
	<br />
	<br />
	<br />
	
	
	<input type="button" title="Io sono un test per la modale" rel="{$html->webroot}testmodal.html'" class="modalbutton" value="modal test example" />
		
		
	<ul style="margin:10px 0px 10px 0px; border:1px solid gray; border-width:1px 0px 1px 0px">
		<li><a href="javascript:$('.main .tab').BEtabsopen();" class="openclose">open all TABS</a></li>
		<li><a href="javascript:$('.main .tab').BEtabsclose();" class="openclose">close all TABS</a></li>
	</ul>	



	</div>


</div>

