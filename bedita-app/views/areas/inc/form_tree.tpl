		
<form id="frmTree" method="post" action="{$html->url('/areas/saveTree')}">
	<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}"/>
	<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}"/>
	<input type="hidden" id="data_tree" name="data[tree]" 			value=""/>
				
	{*$beTree->tree("treeFull", $tree)*}
			
					
	<div class="tab"><h2>Festival di arte contemporanea</h2></div>
	<fieldset>
		<ul class="publishingtree pubmodule">    
			<li><a href="{$html->url('viewSection/')}">festival</a></li>
		        <ul>
					<li>documenti fissi</li>
		        </ul>
		    <li>news</li>
		        <ul>
					<li>news e archivio</li>
		         	<li>iscrizione news</li>
				</ul>
		    <li>programma</li>
				<ul>
					<li>eventi festival</li>
					<li>eventi collaterali</li>
					<li>my festival</li>
				</ul>
			<li>protagonisti</li>
			<li>luoghi</li>
			<li>galleria</li>
			<li>stampa</li>
				<ul>
					<li>comunicati stampa tralla patafolloga e folaghe in volo sui mari del sud</li>
					<li>rassegna</li>
					<li>form di accredito</li>
				 </ul>
			<li>info</li>
			<li>contatti</li>
			<li>partner</li>
			<li>home</li>
		</ul>
		
		
	</fieldset>
				
				
				
	<div class="tab"><h2>Un'altra sezione</h2></div>
	<fieldset>
		<ul class="publishingtree pubmodule">    
			<li>info</li>
			<li>contatti</li>
			<li>partner</li>
			<li>home</li>
		</ul>

	</fieldset>

</form>



