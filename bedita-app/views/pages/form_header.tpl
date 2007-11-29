<h1>{t}{$object.title|default:"New Item"}{/t}</h1>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<a id="openAllBlockLabel" style="display:block;" href="javascript:showAllBlockPage(1)"><span style="font-weight:bold;">&gt;</span> {t}open details{/t}</a>
		<a id="closeAllBlockLabel" href="javascript:hideAllBlockPage()"><span style="font-weight:bold;">&gt;</span> {t}close details{/t}</a>
	</td>
	<td style="padding-left:40px;" nowrap>
		{formHelper fnc="submit" args="' salva ', array('name' => 'save', 'class' => 'submit', 'div' => false)"}
		<input type="button" name="cancella" class="submit" value="{t}cancel{/t}" {if !($object.id|default:false)}disabled="1"{/if}/>
	</td>
	<td style="padding-left:40px">&nbsp;</td>
</tr>
</table>