{*
Template incluso.
Menu a DX
*}


<div class="quartacolonna">	

	<div class="tab"><h2>{t}Notes{/t}</h2></div>

				
	<div style="margin-top:-10px; padding:10px; background-color:white;">
{strip}
	<label>editor notes:</label>
	<textarea name="data[note]" class="autogrowarea" style="line-height: 16px!important; width:156px; font:normal 12px Arial,Helvetica, sans-serif;
	color: #9a5830; background:white url('{$html->webroot}img/sfo_zebranotes.gif'); border:0px;">
	  {$object.note|default:''}
	</textarea>
{/strip}
	<input type="submit" value=" OK ">

	<br /><br />
	
	<label>quick help:</label>

	<div class="help">
		Un fate cazzate qui. Se prorio avete dei dubbi clonate e chiedete
	</div>
	
	

	<hr />
{*	
	<ul>
		<li>icone da fare</li>
		<li>concurrent user</li>
		<li>locked document</li>
		<li>unsaved changes</li>
		<li>trashed document</li>
		<li>user note</li>
	</ul>
	
*}	
	
	
	<input type="button" title="Io sono un test per la modale" rel="{$html->url('/testmodal.html')}" class="modalbutton" value="modal test example" />
		
		
	<ul style="margin:10px 0px 10px 0px; border:1px solid gray; border-width:1px 0px 1px 0px">
		<li><a href="javascript:$('.main .tab').BEtabsopen();" class="openclose">open all TABS</a></li>
		<li><a href="javascript:$('.main .tab').BEtabsclose();" class="openclose">close all TABS</a></li>
	</ul>	



	</div>


</div>

