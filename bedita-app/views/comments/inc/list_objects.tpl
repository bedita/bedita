{$html->script('libs/jquery/plugins/jquery.float_thead.min.js', false)}

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteSelected/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
var urlAddToAreaSection = "{$html->url('addItemsToAreaSection/')}";

{literal}
$(document).ready(function(){

	$('.indexlist').each(function() {
        $(this)
            .width( $(this).closest('.mainfull, .main').outerWidth() )
            .floatThead();
    });

	// avoid to perform double click
	$("a:first", ".indexlist .obj").click(function(e){ 
		e.preventDefault();
	});

	$(".indexlist .obj TD").not(".checklist").css("cursor","pointer").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	
	$("#deleteSelected").bind("click", function() {
		if (!confirm(message)) {
			return false;
		}
		$("#formObject").prop("action", urlDelete);
		$("#formObject").submit() ;
	});
	
	
	$("#assocObjects").click( function() {
		$("#formObject").prop("action", urlAddToAreaSection) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").prop("action", urlChangeStatus) ;
		$("#formObject").submit() ;
	});
});


{/literal}

//-->
</script>	
	
	<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>


	<table class="indexlist">
	{capture name="theader"}
	<thead>
		<tr>
			<th></th>
			<th>{$beToolbar->order('title','Title')}</th>
			<th>{$beToolbar->order('ReferenceObject.title','object title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created','inserted on')}</th>
			<th>{$beToolbar->order('email','email')}</th>
			<th>{$beToolbar->order('ip_created', 'IP')}</th>	
			<th>{$beToolbar->order('id','id')}</th>
		</tr>
	</thead>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td>
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].ReferenceObject.title}</td>
			<td>{$objects[i].status}</td>
			<td>{$objects[i].created|date_format:$conf->dateTimePattern}</td>
			<td>{$objects[i].email|default:''}</td>
			<td>{$objects[i].ip_created}</td>
			<td>{$objects[i].id}</td>
		</tr>
		
		
		
		{sectionelse}
		
			<tr><td colspan="100">{t}No items found{/t}</td></tr>
		
		{/section}

</table>


<br />

{$view->element('list_objects_bulk')}

</form>