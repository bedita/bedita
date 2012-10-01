<script type="text/javascript">
<!--
var g_msg = "{t}Check at least one group{/t}";


function showResponse(data) {
	if (data.SaveErrorMsg) {
		alert(data.SaveErrorMsg);
	} else {
		var id = data.userId;
		var username = $("#userid").val();
		$("#modal").hide();
		$("#modaloverlay").hide();
		addUserToCard(id,username);
	}
}

var optionsUcardForm = {
	//beforeSubmit:	resetError,
	success:		showResponse,  // post-submit callback  
	dataType:		'json'        // 'xml', 'script', or 'json' (expected server response type) 
};

$(document).ready(function() {

	$("#userToCard").click(function() {
		id = $(".uradio:checked").attr("value");
		username = $(".uradio:checked").attr("rel");
		$("#modal").hide();
		$("#modaloverlay").hide();
		addUserToCard(id,username);
	});
	
	$("#createUser").click(function() {
		if($(".ugroup:checked").size() > 0 ) {
			optionsUcardForm.url = "{$this->Html->url('/users/saveUserAjax')}"; // override form action
			$('#ucardForm').ajaxSubmit(optionsUcardForm);
		} else {
			alert(g_msg);
		}
	});
	

	$(".tab").click(function (){
		
		$(this).BEtabstoggle();

	});
	
	var openAtStart ="#selectuser";
	$(openAtStart).prev(".tab").BEtabstoggle();
		

	
});

//-->
</script>

<div class="bodybg" style="height:480px; padding:20px;">
	
<form id="ucardForm" method="post">

<div class="tab"><h2>{t}Select a user from the list{/t}</h2></div>
<fieldset id="selectuser">
<table class="bordered">
	<tr>
		<td></td>
		<th>{t}username{/t}</th>
		<th style="width:50%">{t}realname{/t}</th>
	</tr>
	{foreach from=$users item=u}
	{if empty($u.Card)}
	<tr>
		<td style="text-align:right"><input type="radio" class="uradio" value="{$u.User.id}" rel="{$u.User.userid}" name="usertoassociate"/></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
	</tr>
	{/if}
	{/foreach}
</table>

<input id="userToCard" style="margin:10px 0px 10px 100px" type="button" value=" {t}associate{/t} "/>
</fieldset>

<div class="tab"><h2>{t}Create new user{/t}</h2></div>

<fieldset id="createuser">
<table class="bordered">
	<tr>
		<th style="text-align:right">{t}username{/t}:</th><td><input type="text" name="data[User][userid]" id="userid" /></td>
	</tr>
{if !empty($formGroups)}
{foreach from=$formGroups key=gname item=u}
<tr>
	<td></td>
	<td>
		<input class="ugroup" type="checkbox" id="group_{$gname}" name="data[groups][{$gname}]"/>
		&nbsp;<label id="lgroup{$gname}" for="group{$gname}">{$gname}</label>
		{if in_array($gname,$authGroups)} <span class="evidence">*</span> {/if}
	</td>
</tr>
{/foreach}
{/if}
<tr>
	<td colspan="2"><span class="evidence">*</span> {t}Group authorized to Backend{/t}</td>
</tr>
</table>

<input type="button" style="margin:10px 0px 10px 100px"  id="createUser" value="{t}create and associate{/t}"/>

</fieldset>

</form>

</div>