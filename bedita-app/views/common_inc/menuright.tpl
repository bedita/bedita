{*
Template incluso.
Menu a DX
*}




<script type="text/javascript">
var urlLoadNote = "{$html->url('/pages/loadNote')}";
{literal}
	$(document).ready( function (){
		$("#editornotes").prev(".tab").BEtabsopen();

		var optionsNoteForm = {
			beforeSubmit: function() {$("#noteloader").show();},
			success: showNoteResponse,
			dataType: "json",
			resetForm: true
		}; 
		$("#saveNote").ajaxForm(optionsNoteForm);
	});	

function showNoteResponse(data) {
	if (data.errorMsg) {
		alert(data.errorMsg);
		$("#noteloader").hide();
	} else {
		var emptyDiv = "<div><\/div>";
		$(emptyDiv).load(urlLoadNote, data, function() {
			$("#listNote").append(this);
			$("#noteloader").hide();
		});
	}
}
{/literal}
</script>


<div class="quartacolonna">	
	
	<div class="tab"><h2>{t}Editors Notes{/t}</h2></div>
<!-- old notes 
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{strip}
		<label>{t}editor notes{/t}:</label>
		<textarea name="data[note]" class="autogrowarea editornotes">
		  {$object.note|default:''}
		</textarea>
	{/strip}
	</div>
 end old notes -->
 

 
{bedev}
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{*dump var=$object.EditorNote|@sortby:'id'*}
	{strip}

		<table class="ultracondensed" style="width:100%">
		<tr>
			<td class="author">you</td>
			<td class="date">now</td>
			<td><img src="{$html->webroot}img/iconNotes.gif" alt="notes" /></td>
		</tr>
		</table>
		<form id="saveNote" action="{$html->url('/pages/saveNote')}" method="post">
		<input type="hidden" name="data[object_id]" value="{$object.id}"/>
		<textarea id="notetext" name="data[description]" class="autogrowarea editornotes"></textarea>
		<input type="submit" style="margin-bottom:10px; margin-top:5px" value="{t}send{/t}" />
		</form>
		
		<div class="loader" id="noteloader" style="clear:both">&nbsp;</div>
	
		<div id="listNote">
		{if (!empty($object.EditorNote))}
			{section name=p loop=$object.EditorNote}
				{include file="../common_inc/single_note.tpl" note=$object.EditorNote[p]}
			{/section}
		{/if}
		</div>

	
	{/strip}
	{include file="../common_inc/BEiconstest.tpl}	
	</div>
{/bedev}
</div>


