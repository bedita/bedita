<script type="text/javascript">
$(document).ready(function(){
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


<form action="{$this->Html->url('/users/users')}" method="post" name="userForm" id="userForm">
{assign var='label_id' value=$this->Tr->t('id',true)}
{assign var='label_userid' value=$this->Tr->t('User',true)}
{assign var='label_realname' value=$this->Tr->t('name',true)}
{assign var='label_valid' value=$this->Tr->t('blocked',true)}
{assign var='label_created' value=$this->Tr->t('created',true)}
{assign var='label_last_login' value=$this->Tr->t('last login',true)}
<table class="indexlist">
	<tr>{* TODO: i18n sulle colonne in sort*}
		<th>{$this->Paginator->sort($label_id,'id')}</th>
		<th>{$this->Paginator->sort($label_userid,'userid')}</th>
		<th>{$this->Paginator->sort($label_realname,'realname')}</th>
		<th>{$this->Paginator->sort('email','email')}</th>
		<th>{$this->Paginator->sort($label_valid,'valid')}</th>
		<th>{$this->Paginator->sort($label_created,'created')}</th>
		<th>{$this->Paginator->sort($label_last_login,'last_login')}</th>
		<th>{t}Action{/t}</th>
	</tr>
	{foreach from=$users item=u}
	<tr class="obj">
		<td><a href="{$this->Html->url('/users/viewUser/')}{$u.User.id}">{$u.User.id}</a></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
		<td>{$u.User.email}</td>
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


{if !empty($users)}
	<br/>
	<div style="white-space:nowrap">

		{$pages = $this->Paginator->counter(['format' => '%pages%'])}
		{$pageParams = $this->Paginator->params()}
		
		{t}Go to page{/t}: &nbsp;
		<select id="changePage">
			{for $p=1 to $pages}
			<option value="{$this->Paginator->url(['page' => $p])}"{if $pageParams.options.page == $p} selected="selected"{/if}>{$p}</option>
			{/for}
		</select>
		&nbsp;
		{t}of{/t}&nbsp;
		
		{if $this->Paginator->hasNext()}
			{$this->Paginator->last($pages)}
		{else}
			{$pages}
		{/if}
		
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		{t}Dimensions{/t}: &nbsp;
		<select id="changeDim">
			<option value="{$this->Paginator->url(['limit' => 1])}"{if $pageParams.options.limit == 1} selected="selected"{/if}>1</option>
			<option value="{$this->Paginator->url(['limit' => 5])}"{if $pageParams.options.limit == 5} selected="selected"{/if}>5</option>
			<option value="{$this->Paginator->url(['limit' => 10])}"{if $pageParams.options.limit == 10} selected="selected"{/if}>10</option>
			<option value="{$this->Paginator->url(['limit' => 20])}"{if $pageParams.options.limit == 20} selected="selected"{/if}>20</option>
			<option value="{$this->Paginator->url(['limit' => 50])}"{if $pageParams.options.limit == 50} selected="selected"{/if}>50</option>
			<option value="{$this->Paginator->url(['limit' => 100])}"{if $pageParams.options.limit == 100} selected="selected"{/if}>100</option>
		</select>

		&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;
		{assign var='label_next' value=$this->Tr->t('next',true)}
		{assign var='label_prev' value=$this->Tr->t('prev',true)}
		{$this->Paginator->next($label_next)} <span class="evidence"> &nbsp;</span>	
		| &nbsp;&nbsp;
		{$this->Paginator->prev($label_prev)} <span class="evidence"> &nbsp;</span>
	</div>
		
{/if}

</form>