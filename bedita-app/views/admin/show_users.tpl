<script type="text/javascript">
<!--
var g_msg = "{t}Check at least one group{/t}";
{literal}

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
		username = $(".uradio:checked").attr("name");
		$("#modal").hide();
		$("#modaloverlay").hide();
		addUserToCard(id,username);
	});
	
	$("#createUser").click(function() {
		if($(".ugroup:checked").size() > 0 ) {
			optionsUcardForm.url = "{/literal}{$html->url('/admin/saveUserAjax')}{literal}"; // override form action
			$('#ucardForm').ajaxSubmit(optionsUcardForm);
		} else {
			alert(g_msg);
		}
	});
});
{/literal}
//-->
</script>

<form id="ucardForm" action="" method="post">

{t}Card associated to user from the list{/t}<br/>

<table>
	<tr>
		<th></th>
		<th>{t}User{/t}</th>
		<th>{t}Name{/t}</th>
	</tr>
	{foreach from=$users item=u}
	<tr>
		<td><input type="radio" class="uradio" value="{$u.User.id}" name="{$u.User.userid}"/></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
	</tr>
	{/foreach}
</table>
<input id="userToCard" type="button" value="{t}save{/t}"/>

<br/>
<br/>
<br/>

{t}Card associated to new user{/t}
<br/>
{t}username{/t} <input type="text" name="data[User][userid]" id="userid"/>
<br/>
{t}groups{/t}
<table>
{if !empty($formGroups)}
{foreach from=$formGroups key=gname item=u}
<tr>
	<td>
		<input class="ugroup" type="checkbox" id="group_{$gname}" name="data[groups][{$gname}]"/>
		&nbsp;<label id="lgroup{$gname}" for="group{$gname}">{$gname}</label>
	</td>
	<th>{if in_array($gname,$conf->authorizedGroups)} <span class="evidence">*</span> {/if}</th>
</tr>
{/foreach}
{/if}
<tr>
	<td></td>
	<td><span class="evidence">*</span> {t}Group authorized to Backend{/t}</td>
</tr>
</table>
<input type="button" id="createUser" value="{t}create new user{/t}"/>
</form>