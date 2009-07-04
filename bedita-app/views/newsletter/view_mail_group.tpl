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
		$("#divSubscribers").load($(this).attr("rel"), function() {
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
			$("#divSubscribers").html(htmlcode);
			$("#loaderListSubscribers").hide();
			initSubscribers();
		}	
	);
}

$(document).ready(function() {
	
	openAtStart("#details,#subscribers");

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

<div class="tab"><h2>List details</h2></div>
<fieldset id="details">
	<table class="bordered">
		<tr>
			<td>
				<label for="groupname">{t}list name{/t}:</label>
			</td>
			<td>
				<input type="hidden" name="data[MailGroup][id]" value="{$object.id|default:''}" />
				<input type="text" style="width:360px;" id="groupname" name="data[MailGroup][group_name]" value="{$object.group_name|default:''}" />
			</td>
		</tr>
		<tr>
			<td colspan=2>{assign var='mailgroup_visible' value=$object.visible|default:'1'}
				<input type="radio" name="data[MailGroup][visible]" value="1" {if $mailgroup_visible=='1'}checked="true"{/if}/>
				<label for="visible">{t}public list	{/t}</label> (people can subscribe)
			&nbsp;
				<input type="radio" name="data[MailGroup][visible]" value="0" {if $mailgroup_visible=='0'}checked="true"{/if}/>
				<label for="visible">{t}private list {/t}</label> (back-end insertions only)
			</td>
		</tr>
		<tr>
			<td>
				<label for="publishing">{t}publishing{/t}:</label>
			</td>
			<td>{assign var='mailgroup_area_id' value=$object.area_id|default:''}
				<select style="width:220px" name="data[MailGroup][area_id]">
					{foreach from=$areasList key="area_id" item="public_name"}
					<option value="{$area_id}"{if $area_id == $mailgroup_area_id} selected{/if}>{$public_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		</table>
	</fieldset>
	
<div class="tab"><h2>Config and messages</h2></div>
<fieldset id="configmessages">		
	<table class="bordered">
		<tr>
			<td colspan="2">{assign var='mailgroup_opting_method' value=$object.optingmethod|default:''}
				<label for="optingmethod">{t}subscribing method{/t}:</label>
				&nbsp;&nbsp;
				<select id="optingmethod" name="data[MailGroup][security]">
					<option value="none"{if $mailgroup_opting_method == 'none'} selected{/if}>Single opt-in (no confirmation required)</option>
					<option value="all"{if $mailgroup_opting_method == 'all'} selected{/if}>Double opt-in (confirmation required)</option>
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top">
				<label for="confirmin">{t}Confirmation-In mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_in_message]" id="optinmessage" style="width:220px" class="autogrowarea">{$object.confirmation_in_message|default:''}</textarea>
			</td>
			<td style="vertical-align:top">
				<label for="confirmout">{t}Confirmation-Out mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_out_message]" id="optoutmessage" style="width:220px" class="autogrowarea">{$object.confirmation_out_message|default:''}</textarea>
			</td>
		</tr>
	</table>
</fieldset>

{if !empty($object)}
<div class="tab"><h2>Subscribers</h2></div>
<div id="divSubscribers">{include file="inc/list_subscribers.tpl"}</div>


<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected subscribers{/t}</h2></div>
<fieldset>
		<select name="operation" style="width:75px">
			<option> {t}copy{/t} </option>
			<option> {t}move{/t} </option>
		</select>
		&nbsp;to:&nbsp;
		<select name="destination">
			{if !empty($groups)}
			{foreach from=$groups item="group"}
				{if $group.MailGroup.id != $object.id}
				<option value="{$group.MailGroup.id}">{$group.MailGroup.group_name}</option>
				{/if}
			{/foreach}
			{/if}
		</select>
		<input id="assocCard" type="button" value=" ok " />
	
	<hr />
	
		{t}change status to:{/t}&nbsp;&nbsp;
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value="valid">{t}valid{/t}</option>
			<option value="blocked">{t}blocked{/t}</option>
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
	
	<hr />

	<input id="deleteSelected" type="button" value="X {t}Unsubscribe selected items{/t}"/>
</fieldset>
{/if}

<div class="tab"><h2>Add new subscribers</h2></div>
<fieldset id="">
		Qui si apre un mondo che ppalle, email separate da virgole, check delle preesistenza e tutat cosa che pppppp
		<textarea name="addsubscribers" id="addsubscribers" style="width:100%" class="autogrowarea"></textarea>
</fieldset>

</form>	
	
</div>

{include file="../common_inc/menuright.tpl"}