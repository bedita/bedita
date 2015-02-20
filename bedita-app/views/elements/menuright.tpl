{*
Template incluso.
Menu a DX
*}

<script type="text/javascript">
var urlLoadNote = "{$html->url('/pages/loadNote')}";
var urlDelNote = "{$html->url('/pages/deleteNote')}";
var comunicationErrorMsg = "{t}Communication error{/t}";
var confirmDelNoteMsg = "{t}Are you sure that you want to delete the note?{/t}";


$(document).ready( function (){
	$("#editornotes").prev(".tab").BEtabsopen();

	var optionsNoteForm = {
		beforeSubmit: function() { $("#noteloader").show();},
		success: showNoteResponse,
		dataType: "json",
		resetForm: true,
		error: function() {
			alert(comunicationErrorMsg);
			$("#noteloader").hide();
		}
	}; 
	$("#saveNote").ajaxForm(optionsNoteForm);
	
	$("#listNote").find("input[name=deletenote]").click(function() {
		refreshNoteList($(this));
	});
});	

function showNoteResponse(data) {
	if (data.errorMsg) {
		alert(data.errorMsg);
		$("#noteloader").hide();
	} else {
		var emptyDiv = "<div><\/div>";
		$(emptyDiv).load(urlLoadNote, data, function() {
			$("#listNote").prepend(this);
			$("#noteloader").hide();
			$(this).find("input[name=deletenote]").click(function() {
				refreshNoteList($(this));
			});
		});
	}
}

function refreshNoteList(delButton) {
	var div = delButton.parents("div:first");
	var postdata = { id: delButton.attr("rel")};
	// add csrf token if exists
	addCsrfToken(postdata, '#saveNote');
	if (confirm(confirmDelNoteMsg)) {
		$.ajax({ 
			type: "POST",
			url: urlDelNote,
			data: postdata,
			dataType: "json",
			beforeSend: function() { $("#noteloader").show();},
			success: function(data){ 
				if (data.errorMsg) { 
					alert(data.errorMsg);
					$("#noteloader").hide();
				} else {
					$("#noteloader").hide();
					div.remove();
				}
			},
			error: function() {
				alert(comunicationErrorMsg);
				$("#noteloader").hide();
			}
		});
	}
}

</script>


<div class="quartacolonna">	

<!-- ///// notes ////// -->

{if !empty($object)}

	<div class="tab"><h2>{t}Notes{/t}</h2></div>
 
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{*dump var=$object.EditorNote|@array_reverse*}
	{strip}

		<table class="ultracondensed" style="width:100%">
		<tr>
			<td class="author">you</td>
			<td class="date">now</td>
			<td><img src="{$html->webroot}img/iconNotes.gif" alt="notes" /></td>
		</tr>
		</table>
		<form id="saveNote" action="{$html->url('/pages/saveNote')}" method="post">
		{$beForm->csrf()}
		<input type="hidden" name="data[object_id]" value="{$object.id}"/>
		<textarea id="notetext" name="data[description]" class="autogrowarea editornotes"></textarea>
		<input type="submit" style="margin-bottom:10px; margin-top:5px" value="{t}send{/t}" />
		</form>
		
		<div class="loader" id="noteloader" style="clear:both">&nbsp;</div>
	
		<div id="listNote">
		{if (!empty($object.EditorNote))}
			{foreach from=$object.EditorNote|@array_reverse item="note"}
				{assign_associative var="params" note=$note}
				{$view->element('single_note', $params)}
			{/foreach}
		{/if}
		</div>
	
	{/strip}
	</div>

{/if}

<!-- ///// related tickets ////// -->

{if !empty($relObjects.ticketRelated)}
	<div class="tab"><h2>{t}Related tickets{/t}</h2></div>
	<div id="ticketRelated">
		<ul class="bordered">
		{foreach from=$relObjects.ticketRelated item=item}
			<li style="padding-left:5px;" class="{$item.status} {$item.ticket_status}">
				<a href="{$html->url('/')}{$item.ObjectType.module_name}/view/{$item.id}">
				<span title="{$item.ObjectType.name}" class="listrecent {$item.ObjectType.module_name}" style="margin:0 10px 0 0">&nbsp;</span>
				{$item.title|escape|default:'<i>[no title]</i>'|truncate:30:'~':true}</a>
			</li>
		{/foreach}
			<li style="padding-left:5px;">
				<a href="{$html->url('/')}{$item.ObjectType.module_name}/view/">
				<span class="listrecent {$item.ObjectType.module_name}" style="margin:0px">&nbsp;</span>
				&nbsp;&nbsp;{t}create new{/t} ticket</a>
			</li>			
		</ul>
	</div>
{/if}	
	
	{bedev}
		<div class="tab"><h2>{t}Test stuff{/t}</h2></div>
		<div id="test" style="padding:10px; background-color:white;">
		{$view->element('BEiconstest')}
		</div>
	{/bedev}


</div>