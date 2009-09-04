<script type="text/javascript">
{literal}
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
});

{/literal}
//-->
</script>	


<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">
{assign var='label_id' value=$tr->t('id',true)}
{assign var='label_userid' value=$tr->t('User',true)}
{assign var='label_realname' value=$tr->t('name',true)}
{assign var='label_valid' value=$tr->t('blocked',true)}
{assign var='label_created' value=$tr->t('created',true)}
{assign var='label_last_login' value=$tr->t('last login',true)}
<table class="indexlist">
	<tr>{* TODO: i18n sulle colonne in sort*}
		<th>{$paginator->sort($label_id,'id')}</th>
		<th>{$paginator->sort($label_userid,'userid')}</th>
		<th>{$paginator->sort($label_realname,'realname')}</th>
		<th>{$paginator->sort($label_valid,'valid')}</th>
		<th>{$paginator->sort($label_created,'created')}</th>
		<th>{$paginator->sort($label_last_login,'last_login')}</th>
		<th>{t}Action{/t}</th>
	</tr>
	{foreach from=$users item=u}
	<tr>
		<td><a href="{$html->url('/admin/viewUser/')}{$u.User.id}">{$u.User.id}</a></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
		<td>{if $u.User.valid=='1'}{t}No{/t}{else}{t}Yes{/t}{/if}</td>
		<td>{$u.User.created|date_format:$conf->dateTimePattern}</td>
		<td>{$u.User.last_login|date_format:$conf->dateTimePattern}</td>
		<td class="go">
			{if $module_modify eq '1' && $BEAuthUser.userid ne $u.User.userid}
			<input type="button" name="removeUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			{/if}
		</td>
	{/foreach}
</table>

</form>