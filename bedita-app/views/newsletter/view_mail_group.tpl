{*
** subscriber view template
*}

{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}

<script type="text/javascript">
<!--
var urlListSubscribers = "{$html->url('/newsletter/listSubscribers')}";

function initSubscribers() {

	$("#paginateSubscribers a, #orderSubscribers a").each(function() {
		searched = "viewMailGroup";
		specificParams = $(this).attr("href");
		position = specificParams.indexOf(searched);
		if (position == -1) {
			searched = "listSubscribers";
			position = specificParams.indexOf(searched);
		}
		position += searched.length;
		specificParams = specificParams.substr(position);
		$(this).attr("rel", urlListSubscribers + specificParams).attr("href", "javascript: void(0);");
	});
	
	$("#paginateSubscribers a, #orderSubscribers a").click(function() {
		$("#loaderListSubscribers").show();
		$("#subscribers").load($(this).attr("rel"), function() {
			$("#loaderListSubscribers").hide();
			initSubscribers();
		});
	});
}

// get form params and perform a post action
function submitSubscribers(url) {
	$("#loaderListSubscribers").show();
	var arrVal = new Array();
	$("input.objectCheck:checked").each(function(index) {
		arrVal[index] = $(this).val();
	});
	
    var postData = {
            'objects_selected[]': arrVal,
            'operation': $("select[name=operation]").val(),
            'destination': $("select[name=destination]").val(),
            'newStatus': $("select[name=newStatus]").val()
        };
    postData = addCsrfToken(postData, '#updateForm');
    $.post(url, postData,
		function(htmlcode) {
			$("#subscribers").html(htmlcode);
			$("#loaderListSubscribers").hide();
			initSubscribers();
		}	
	);
}

$(document).ready(function() {
	
	openAtStart("#details,#divSubscribers,#addsubscribers");

	initSubscribers();
	
	$("#assocCard").click( function() {
		submitSubscribers("{$html->url('/newsletter/addCardToGroup/')}{$item.id|default:''}");		
	});
	
	$("#changestatusSelected").click( function() {
		submitSubscribers("{$html->url('/newsletter/changeCardStatus/')}{$item.id|default:''}");
	});

	$("#deleteSelected").bind("click", function() {
		if(!confirm("{t}Do you want unsubscribe selected items?{/t}")) 
			return false ;	
		submitSubscribers("{$html->url('/newsletter/unlinkCard/')}{$item.id|default:''}");
	});
});

//-->
</script>

{assign var="delparam" value="/newsletter/deleteMailGroups"}

{$view->element('form_common_js')}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="mailgroups"}

<div class="head">
	
	<h1>{$item.group_name|escape|default:"New List"}</h1>
	
</div>

{include file="inc/menucommands.tpl" method="viewmailgroup" fixed = true}

<div class="main">	

<form method="post" id="updateForm" action="{$html->url('saveMailGroups')}">	
{$beForm->csrf()}

{include file="inc/list_details.tpl"}

{include file="inc/form_subscribers.tpl"}


{include file="inc/list_config_messages.tpl"}

</form>	
	
</div>

{$view->element('menuright')}