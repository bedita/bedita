<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		<label class="admin">
		{if $view->action eq 'profile'}
			{t}profile{/t}
		{elseif $view->action eq 'import' || $view->action eq 'importData'}
			{t}Import Data{/t}
		{/if}
		</label>
	</div> 		

	<div class="insidecol">
	</div>

</div>
	


	



