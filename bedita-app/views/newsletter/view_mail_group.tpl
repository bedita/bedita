{*
** subscriber view template
*}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}

<script type="text/javascript">
<!--
var urlListSubscribers = "{$html->url('/newsletter/listSubscribers')}";

{literal}
function initSubscribers() {

	$("#paginateSubscribers a, #orderSubscribers a").each(function() {
		searched = "view_mail_group";
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
	
	$.post(url,
		{
			'objects_selected[]': arrVal,
			'operation': $("select[@name=operation]").val(),
			'destination': $("select[@name=destination]").val(),
			'newStatus': $("select[@name=newStatus]").val()
		},
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
		submitSubscribers("{/literal}{$html->url('/newsletter/addCardToGroup/')}{$object.id|default:''}{literal}");		
	});
	
	$("#changestatusSelected").click( function() {
		submitSubscribers("{/literal}{$html->url('/newsletter/changeCardStatus/')}{$object.id|default:''}{literal}");
	});

	$("#deleteSelected").bind("click", function() {
		if(!confirm("{/literal}{t}Do you want unsubscribe selected items?{/t}{literal}")) 
			return false ;	
		submitSubscribers("{/literal}{$html->url('/newsletter/unlinkCard/')}{$object.id|default:''}{literal}");
	});
});
{/literal}
//-->
</script>

{assign var="delparam" value="/newsletter/deleteMailGroups"}
{include file="../common_inc/form_common_js.tpl"}

</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="mailgroups"}

<div class="head">
	
	<h1>{t}{$object.group_name|default:"New List"}{/t}</h1>
	
</div>

{include file="inc/menucommands.tpl" method="viewmailgroup" fixed = true}

<div class="main">	

<form method="post" id="updateForm" action="{$html->url('saveMailGroups')}">	

{include file="inc/list_details.tpl"}

{include file="inc/form_subscribers.tpl"}


{include file="inc/list_config_messages.tpl"}

</form>	
	
</div>

{include file="../common_inc/menuright.tpl"}