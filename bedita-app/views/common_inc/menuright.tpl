{*
Template incluso.
Menu a DX
*}


<div class="quartacolonna">	

	<div class="tab"><h2>{t}Notes{/t}</h2></div>

	<div style="padding:10px; margin-top:-10px; background-color:white;">
				
	
	<ul>
		<li>concurrent user</li>
		<li>locked document</li>
		<li>unsaved changes</li>
		<li>trashed document</li>
		<li>user note</li>
	</ul>
	
	
	
	
	<input type="button" title="Io sono un test per la modale" rel="{$html->url('/testmodal.html')}" class="modalbutton" value="modal test example" />
		
		
	<ul style="margin:10px 0px 10px 0px; border:1px solid gray; border-width:1px 0px 1px 0px">
		<li><a href="javascript:$('.main .tab').BEtabsopen();" class="openclose">open all TABS</a></li>
		<li><a href="javascript:$('.main .tab').BEtabsclose();" class="openclose">close all TABS</a></li>
	</ul>	
		
		
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer magna tortor, scelerisque vitae, pharetra eu, 
		<ul class="bulleted">
			<li>venenatis ac, erat. </li>
			<li>In ultricies, turpis vel laoreet scelerisque</li>
			<li>elit tellus porttitor enim</li>
			<li>ac consequat metus tellus</li>
			<li>in nisl. Vestibulum posuere</li>
			<li>dui at mattis fermentum</li>
			<li>odio augue pellentesque massa</li>	
		</ul>
		
		
		<pre>
			{dump var=$currentModule}
		</pre>



	</div>



</div>

