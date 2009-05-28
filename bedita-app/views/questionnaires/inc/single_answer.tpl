<tr>
	<td style="vertical-align:top; white-space:nowrap; cursor:move; padding:2px 5px; border:0px solid red;">
		: :
	</td>
	<td style="vertical-align:top; padding-left:5px;">
		<input class="iteration" tabindex="5000" type="text" style="text-align:center; margin-right:10px; width:20px" name="data[QuestionAnswer][{$i}][priority]" value="{$it}" />
	</td>
	<td><textarea style="height:32px; width:270px !important;" name="data[QuestionAnswer][{$i}][description]" class="autogrowarea">{$answer.description|default:''}</textarea></td>
	<td>
		&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$i}][correct]" value="1" 
		{if @$answer.correct == 1} checked="checked"{/if}>&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
	</td>
	<td>
		<input type="button" class="add" title="{t}add{/t}" value="+" />
	</td>
	<td>
		<input type="button" class="remove" title="{t}remove{/t}" value="-" />
		<input type="button" style="display:none;" class="undo" value="{t}u{/t}" />
	</td>
</tr>