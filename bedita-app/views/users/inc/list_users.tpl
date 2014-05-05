{$html->script('libs/jquery/plugins/jquery.float_thead.min.js', false)}

<script type="text/javascript">
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

	$(".indexlist .obj TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	$("#changeDim, #changePage").change(function() {
		document.location = $(this).val();
	});
});
//-->
</script>	


<form action="{$html->url('/users/users')}" method="post" name="userForm" id="userForm">
{assign var='label_id' value=$tr->t('id',true)}
{assign var='label_userid' value=$tr->t('User',true)}
{assign var='label_realname' value=$tr->t('name',true)}
{assign var='label_valid' value=$tr->t('blocked',true)}
{assign var='label_created' value=$tr->t('created',true)}
{assign var='label_last_login' value=$tr->t('last login',true)}
{$labelAuth = $tr->t('auth',true)}
<table class="indexlist">
	<thead>
		<tr>{* TODO: i18n sulle colonne in sort*}
			<th>{$paginator->sort($label_id,'id')}</th>
			<th>{$paginator->sort($label_userid,'userid')}</th>
			<th>{$paginator->sort($label_realname,'realname')}</th>
			<th>{$paginator->sort('email','email')}</th>
			<th>{$paginator->sort($label_valid,'valid')}</th>
			<th>{$paginator->sort($label_created,'created')}</th>
			<th>{$paginator->sort($label_last_login,'last_login')}</th>
	        <th>{$paginator->sort($labelAuth,'auth_type')}</th>
			<th>{t}Action{/t}</th>
		</tr>
	</thead>
	{foreach from=$users item=u}
	<tr class="obj">
		<td><a href="{$html->url('/users/viewUser/')}{$u.User.id}">{$u.User.id}</a></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
		<td>{$u.User.email}</td>
		<td>{if $u.User.valid=='1'}{t}No{/t}{else}{t}Yes{/t}{/if}</td>
		<td>{$u.User.created|date_format:$conf->dateTimePattern}</td>
		<td>{$u.User.last_login|date_format:$conf->dateTimePattern}</td>
        <td>{$u.User.auth_type|default:'BEdita'}</td>
		<td class="go">
			{if $module_modify eq '1' && $BEAuthUser.userid ne $u.User.userid}
			<input type="button" name="removeUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id},{$u.User.related_obj|default:0},{$u.User.valid});"/>
			{/if}
		</td>
	{/foreach}
</table>


{if !empty($users)}
	<br/>
	<div style="white-space:nowrap">

		{$pages = $paginator->counter(['format' => '%pages%'])}
		{$pageParams = $paginator->params()}
		
		{t}Go to page{/t}: &nbsp;
		<select id="changePage">
			{for $p=1 to $pages}
			<option value="{$paginator->url(['page' => $p])}"{if $pageParams.options.page == $p} selected="selected"{/if}>{$p}</option>
			{/for}
		</select>
		&nbsp;
		{t}of{/t}&nbsp;
		
		{if $paginator->hasNext()}
			{$paginator->last($pages)}
		{else}
			{$pages}
		{/if}
		
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		{t}Dimensions{/t}: &nbsp;
		<select id="changeDim">
			<option value="{$paginator->url(['limit' => 1])}"{if $pageParams.options.limit == 1} selected="selected"{/if}>1</option>
			<option value="{$paginator->url(['limit' => 5])}"{if $pageParams.options.limit == 5} selected="selected"{/if}>5</option>
			<option value="{$paginator->url(['limit' => 10])}"{if $pageParams.options.limit == 10} selected="selected"{/if}>10</option>
			<option value="{$paginator->url(['limit' => 20])}"{if $pageParams.options.limit == 20} selected="selected"{/if}>20</option>
			<option value="{$paginator->url(['limit' => 50])}"{if $pageParams.options.limit == 50} selected="selected"{/if}>50</option>
			<option value="{$paginator->url(['limit' => 100])}"{if $pageParams.options.limit == 100} selected="selected"{/if}>100</option>
		</select>

		&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;
		{assign var='label_next' value=$tr->t('next',true)}
		{assign var='label_prev' value=$tr->t('prev',true)}
		{$paginator->next($label_next)} <span class="evidence"> &nbsp;</span>	
		| &nbsp;&nbsp;
		{$paginator->prev($label_prev)} <span class="evidence"> &nbsp;</span>
	</div>
		
{/if}

</form>