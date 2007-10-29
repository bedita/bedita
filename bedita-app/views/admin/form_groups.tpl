<div id="containerPage">
	<div class="FormPageHeader"><h1>{t}Groups admin{/t}</h1></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveGroup')}" method="post" name="groupForm" id="groupForm">
		<table class="indexList">
		<thead><tr><th>{t}Id{/t}</th>
					<th>{t}Name{/t}</th>
					<th>{t}Created{/t}</th>
					<th>{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$groups item=g}
		<tr class="rowList">
				<td>{$g.Group.id}</td>
			{if $g.Group.id gt 10003}
				<td><a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a></td>
				<td>{$g.Group.created}</td>
				<td>
					<a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{t}Modify{/t}</a>
					<a href="{$html->url('/admin/removeGroup/')}{$g.Group.id}">{t}Remove{/t}</a>
				</td>
			{else}
				<td>{$g.Group.name}</td>
				<td>-</td>
				<td>-</td>
			{/if}
		</tr>
  		{/foreach}
  		</tbody>
		</table>
				
		<h2 class="showHideBlockButton">{t}Group properties{/t}</h2>
		
		<div id="groupForm">
			<table border="0" cellspacing="8" cellpadding="0">
			<tr>
				 	{if isset($group)}
					<input type="hidden" name="data[Group][id]" value="{$group.Group.id}"/>
					{/if}
					<td>{t}Name{/t}</td>
					<td><input type="text" name="data[Group][name]" value="{$group.Group.name}"/>&nbsp;</td>
			</tr>
			<tr>
			<td colspan="2">
				<input type="submit" name="save" class="submit" value="{if isset($group)}{t}Modify{/t}{else}{t}Create group{/t}{/if}" />
			</td> 
		</tr>
			</table>		
		</div>
		</form>
				
	</div>
</div>