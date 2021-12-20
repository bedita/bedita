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
		{$bePaginatorToolbar->show('compact')}
	</div>
{/if}
