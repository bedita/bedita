{*
** subscriber view template
*}

{$html->css("ui.datepicker")}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.selectboxes.pack")}
{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}
{literal}
<script type="text/javascript">
	$(document).ready( function () {
		var openAtStart ="#details,#subscribers";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
{/literal}

{include file="../common_inc/form_common_js.tpl"}

</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="mailgroups"}

<div class="head">
	
	<h1>{t}{$object.group_name|default:"New List"}{/t}</h1>
	
</div>


{include file="inc/menucommands.tpl" method="viewmailgroup" fixed = true}


<div class="main">	

<form method="post" id="updateForm" action="{$html->url('saveMailGroups')}">	

<div class="tab"><h2>List details</h2></div>
<fieldset id="details">
	<table class="bordered">
		<tr>
			<td>
				<label for="groupname">{t}list name{/t}:</label>
			</td>
			<td>
				<input type="hidden" name="data[MailGroup][id]" value="{$object.id|default:''}" />
				<input type="text" id="groupname" name="data[MailGroup][group_name]" value="{$object.group_name|default:''}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="publishing">{t}publishing{/t}:</label>
			</td>
			<td>{assign var='mailgroup_area_id' value=$object.area_id|default:''}
				<select style="width:220px" name="data[MailGroup][area_id]">
					{foreach from=$areasList key="area_id" item="public_name"}
					<option value="{$area_id}"{if $area_id == $mailgroup_area_id} selected{/if}>{$public_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>{assign var='mailgroup_visible' value=$object.visible|default:'1'}
				<input type="radio" name="data[MailGroup][visible]" value="1" {if $mailgroup_visible=='1'}checked="true"{/if}/>
				<label for="visible">{t}public list	{/t}</label> (people can subscribe)
			&nbsp;
				<input type="radio" name="data[MailGroup][visible]" value="0" {if $mailgroup_visible=='0'}checked="true"{/if}/>
				<label for="visible">{t}private list {/t}</label> (back-end insertions only)
			</td>
		</tr>
		</table>
	</fieldset>
	
<div class="tab"><h2>Config and messages</h2></div>
<fieldset id="configmessages">		
	<table class="bordered">
		<tr>
			<td colspan="2">{assign var='mailgroup_opting_method' value=$object.optingmethod|default:''}
				<label for="optingmethod">{t}subscribing method{/t}:</label>
				&nbsp;&nbsp;{$mailgroup_opting_method}
				<select id="optingmethod" name="data[MailGroup][security]">
					<option value="none"{if $mailgroup_opting_method == 'none'} selected{/if}>Single opt-in (no confirmation required)</option>
					<option value="all"{if $mailgroup_opting_method == 'all'} selected{/if}>Double opt-in (confirmation required)</option>
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top">
				<label for="confirmin">{t}Confirmation-In mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_in_message]" id="optinmessage" style="width:220px" class="autogrowarea">{$object.confirmation_in_message|default:''}</textarea>
			</td>
			<td style="vertical-align:top">
				<label for="confirmout">{t}Confirmation-Out mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_out_message]" id="optoutmessage" style="width:220px" class="autogrowarea">{$object.confirmation_out_message|default:''}</textarea>
			</td>
		</tr>
	</table>
</fieldset>

<div class="tab"><h2>Subscribers</h2></div>
<fieldset id="subscribers">		
		<table class="indexlist">
			<tr>
				<th></th>
				<th>email</th>
				<th>html</th>
				<th>status</th>
				<th>inserted on</th>
				<th></th>
			</tr>
			<tr>
				<td><input type="checkbox" /></td>
				<td>carrachio@madiovavavava.com</td>
				<td>yes</td>
				<td>on</td>
				<td>12-12-2008</td>
				<td><a href="{*$html->url('/addressbook/view/')}{$objects[i].id*}">› details</a></td>
			</tr>
			<tr>
				<td><input type="checkbox" /></td>
				<td>carchio@madioava.com</td>
				<td>yes</td>
				<td>on</td>
				<td>12-02-2008</td>
				<td><a href="{*$html->url('/addressbook/view/')}{$objects[i].id*}">› details</a></td>
			</tr>
			<tr>
				<td><input type="checkbox" /></td>
				<td>carrachmadio@vavavava.com</td>
				<td>yes</td>
				<td>on</td>
				<td>12-12-2008</td>
				<td><a href="{*$html->url('/addressbook/view/')}{$objects[i].id*}">› details</a></td>
			</tr>
		</table>
		<hr />


		<table class="graced">
		<tr>
			<td>
				{$beToolbar->first('page','','page')}
				<span class="evidence"> 1 </span> 
				{t}of{/t} 
				<span class="evidence"> 
					2
				</span>
				&nbsp;
			</td>
			<td style="border:1px solid gray; border-top:0px; border-bottom:0px;">{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
			<td>{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>
		</tr>
		</table>





</fieldset>


<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected subscribers{/t}</h2></div>
<fieldset>
		<select style="width:75px">
			<option> {t}copy{/t} </option>
			<option> {t}move{/t} </option>
		</select>
		&nbsp;to:&nbsp;
		<select>
			<option>qui l'elenco delelliste uffa e riufa  </option>
		</select>
		<input id="assocObjects" type="button" value=" ok " />
	
	<hr />
	
		{t}change status to:{/t}&nbsp;&nbsp;
		<select style="width:75px" id="newStatus" name="newStatus">
		<option value=""> -- </option>
		{html_options options=$conf->statusOptions}
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
	
	<hr />

	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</fieldset>


<div class="tab"><h2>Add new subscribers</h2></div>
<fieldset id="subscribers">
		Qui si apre un mondo che ppalle, email separate da virgole, check delle preesistenza e tutat cosa che pppppp
		<textarea id="addsubscribers" style="width:100%" class="autogrowarea"></textarea>
</fieldset>


</form>	
	
</div>


{include file="../common_inc/menuright.tpl"}





