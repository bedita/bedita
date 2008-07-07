{*
Template incluso.
Menu comandi a SX valido per tutte le pagine del controller.
*}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}"
		{literal}
	});
	
	$("div.insidecol input[@name='save']").click(function() {
		$("#updateForm").submit();
	});
});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="areas" href="{$html->url('/areas')}">{t}Publishing{/t}</label>
	</div> 
	
	{include file="../common_inc/messages.tpl"}
			

	<div class="insidecol">
		
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />	
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />

	
	</div>

	<br>
	
	<ul class="insidecol">
		<li><a href="#">{t}Close all{/t}</a></li>
		<li><a href="#">{t}Expand all{/t}</a></li>
	</ul>



</div>


