{literal}
<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("table[id]");
    });
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Manage custom relations{/t}</h1>
</div>

<div class="main">

<form id="customRelations">
{foreach from=$conf->objRelationType item=item key=keyname}

	<div class="tab"><h2>{$keyname}</h2></div>
		
	<table class="bordered" style="margin-bottom:20px;" id="{$keyname}">
		<tr>
			<th><label>source</label></th>
			<td>
				<select multiple>
					<option>all</option>
					<optgroup label="-----------"></optgroup>
				{foreach from=$conf->objectTypes item=type key=key}	
					{if ( is_numeric($key) )}
					<option {if (in_array($type.name, $item.left))}selected=1{/if}>	
						{t}{$type.model}{/t}
					</option>
					{/if}
				{/foreach}
				</select>

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				→						
			</td>
			<th>
				<label>target</label>
			</th>
			<td>
				<select multiple>
					<option>all</option>
					<optgroup label="-----------"></optgroup>
				{foreach from=$conf->objectTypes item=type key=key}	
					{if ( is_numeric($key) )}
					<option {if (in_array($type.name, $item.right))}selected=1{/if}>	
						{t}{$type.model}{/t}
					</option>
					{/if}
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<th><label>{t}name{/t}</label></th>
			<td><input type="text" value="{$keyname}"></td>
			<th><label>{t}inverse name{/t}</label></th>
			<td><input type="text" value="{$item.inverse|default:''}"></td>
			<td></td>
		</tr>
		<tr>
			<th><label>{t}label{/t}</label></th>
			<td><input type="text" value="{$item.name|default:''}" /></td>
			<th><label>inverse label</label></th>
			<td><input type="text" value="{$item.inverselabel|default:''}"></td>
		</tr>
	</table>
{/foreach}
</form>


</div>