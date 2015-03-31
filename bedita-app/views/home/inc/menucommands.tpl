{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		<label class="admin">
		{if $view->action eq 'profile'}
			{t}profile{/t}
		{elseif $view->action eq 'import'}
			{t}Import Data{/t}
		{/if}
		</label>
	</div> 		

	<div class="insidecol">
		<input class="bemaincommands" type="button" name="save" onClick="$('#editProfile').submit()" value="{t}save{/t}" />
	</div>

</div>
	


	



