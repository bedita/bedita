{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		<label class="admin">{t}profile{/t}</label>
	</div> 		

	<div class="insidecol">
		<input class="bemaincommands" type="button" name="save" onClick="$('#editProfile').submit()" value="{t}save{/t}" />
	</div>

</div>
	


	



