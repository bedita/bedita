<script type="text/javascript">
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	$("#changeDim, #changePage").change(function() {
		document.location = $(this).val();
	});

	$('.indexlist form').submit(function(e) {
		var submitButton = $(this).find('input[type=submit]');
		var name = submitButton.attr('data-name');
		if (!confirm("{t}Do you really want to remove group{/t} " + name + "?")) {
			return false;
		}
	});
});
</script>


{assign var='p_name' value=$tr->t('name',true)}
{assign var='p_modified' value=$tr->t('modified',true)}
<table class="indexlist js-header-float">
	<thead>
		<tr>
			<th>{$paginator->sort($p_name,'name')}</th>
			<th>{t}Access to Backend{/t}</th>
			<th style="text-align:center">{t}Users{/t}</th>
			<th>{$paginator->sort($p_modified,'modified')}</th>
			<th></th>
		</tr>
	</thead>
	{foreach from=$groups|default:'' item=g}
	<tr class="rowList {if ($g.Group.id == $group.Group.id)}on{/if}">	
		<td>
			<a href="{$html->url('/users/viewGroup/')}{$g.Group.id}">{$g.Group.name|escape}</a>
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
				<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
			</td>
		{else}
			<td class="go" style="text-align:center">
				<form action="{$html->url('/users/removeGroup')}" method="post">
				{$beForm->csrf()}
				<input type="hidden" name="data[Group][id]" value="{$g.Group.id}"/>
				<input type="submit" name="removeGroup" value="{t}Delete{/t}" data-name="{$g.Group.name|escape}"/>
				</form>
			</td>
		{/if}
		
	</tr>
  	{/foreach}
</table>

{if !empty($groups)}
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
