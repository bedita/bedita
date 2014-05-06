<div class="bodybg">

	<div class="trigger">{t}Search{/t}</div>
	<div class="search" >
		<table class="filters" style="width:100%">
			<tbody>
				<tr>
					<th><label>{t}search user{/t}:</label></th>
					<td colspan="6">
						<input type="text" placeholder="{t}search user{/t}" name="filter[query]" id="search" style="width:255px" value="{$view->SessionFilter->read('query')}"/>
					</td>
				</tr>

				<tr>
					<th></th>
					<td colspan="10">
						<input type="button" id="addUserToGroupSearch" style="width:150px" value=" {t}find it{/t} ">
						<input type="button" id="addUserToGroupClean" value=" {t}reset{/t} ">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div id="loadUsersInModal" class="loader"><span></span></div>

	<div id="listUsers">
		{include file='./inc/form_users_to_associate.tpl'}
	</div>

</div>

<div class="modalcommands">

	<input id="addUserToGroup" style="margin-bottom:10px;" type="button" value=" {t}add to group{/t} "  data-value=" {t}add to group{/t} "/>

</div>