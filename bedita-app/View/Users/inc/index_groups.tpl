<form action="{$this->Html->url('/users/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">

<script type="text/javascript">
$(document).ready(function(){
	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	$("#changeDim, #changePage").change(function() {
		document.location = $(this).val();
	});
});
</script>


{assign var='p_name' value=$this->Tr->t('name',true)}
{assign var='p_modified' value=$this->Tr->t('modified',true)}
<table class="indexlist">
	<tr>
		<th>{$this->Paginator->sort($p_name,'name')}</th>
		<th>{t}Access to Backend{/t}</th>
		<th style="text-align:center">{t}Users{/t}</th>
		<th>{$this->Paginator->sort($p_modified,'modified')}</th>
		<th></th>
	</tr>
	{foreach from=$groups|default:'' item=g}
	<tr class="rowList {if ($g.Group.id == $group.Group.id)}on{/if}">	
		<td>
			<a href="{$this->Html->url('/users/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a>
		</td>
		<td>
			{if $g.Group.backend_auth}{t}Authorized{/t}{else}{t}Not Authorized{/t}{/if}
		</td>
		<td style="text-align:center">
			{$g.Group.num_of_users}
		</td>
		<td>{$g.Group.modified}</td>
		{if $g.Group.immutable or $module_modify != '1'}	
			<td style="text-align:center">
				<img title="{t}fixed object{/t}" src="{$this->Html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
			</td>
		{else}
			<td class="go" style="text-align:center">
				<input type="button" name="deleteGroup" value="{t}Delete{/t}" 
				onclick="javascript:delGroupDialog('{$g.Group.name}',{$g.Group.id});"/>
			</td>
		{/if}
		
	</tr>
  	{/foreach}
</table>

{if !empty($groups)}
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
