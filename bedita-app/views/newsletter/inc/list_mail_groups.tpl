<script type="text/javascript">
var urlDelete = "{$html->url('deleteMailGroups/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";

$(document).ready(function(){
	$(".delete").bind("click", function(){
		if(!confirm(message)) return false ;
		var groupId = $(this).prop("title");
		$("#form_"+groupId).prop("action", urlDelete).submit();
		return false;
	});
	
});

</script>

	<table class="indexlist js-header-float">
		<thead>
			<tr>
				<th>{t}list name{/t}</th>
				<th>{t}status{/t}</th>
				<th>{t}subscribers{/t}</th>
				<th>{t}publication{/t}</th>
				<th>Id</th>
			</tr>
		</thead>

		{foreach from=$mailGroups item="grp" name="fc"}

			<tr rel="{$html->url('/newsletter/viewMailGroup/')}{$grp.id}">
				<td>
					{$grp.group_name|escape}
				</td>
				<td>
					{if $grp.visible == "1"}
						public
					{elseif $grp.visible == "0"}
						hidden
					{/if}
				</td>
				<td>
					{$grp.subscribers}
				</td>
				<td>
					{$grp.publishing|escape}
				</td>
				<td>{$grp.id}</td>
			</tr>
			

		{foreachelse}	
			<tr><td colspan="5">{t}No mail group found{/t}</td></tr>
		{/foreach}		
		</table>