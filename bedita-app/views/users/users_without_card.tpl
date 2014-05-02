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
						<input type="button" id="promoteUserSearch" style="width:150px" value=" {t}find it{/t} ">
						<input type="button" id="promoteUserClean" value=" {t}reset{/t} ">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div id="loadUsersInModal" class="loader"><span></span></div>

	<div id="listUsers">
		{include file='./inc/form_users_to_promote.tpl'}
	</div>

</div>

<div class="modalcommands">

	<input id="userToCard" style="margin-bottom:10px;" type="button" value=" {t}associate{/t} "/>

	<input id="createUser" style="margin: 0 0 10px 10px" type="button" value=" {t}create new user{/t} "/>

</div>