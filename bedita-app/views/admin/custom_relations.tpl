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

	<div class="tab"><h2>{t}Create a new custom relation{/t}</h2></div>

	<form>
		<table class="bordered">
			<tr>
				<th><label>source</label></th>
				<td>
					<select multiple>
						<option>all</option>
						<optgroup label="-----------"></optgroup>
					{foreach from=$conf->objectTypes item=type key=key}	
						{if ( is_numeric($key) )}
						<option>	
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
						<option>	
							{t}{$type.model}{/t}
						</option>
						{/if}
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th><label>{t}name{/t}</label></th>
				<td><input type="text"></td>
				<th><label>{t}inverse name{/t}</label></th>
				<td><input type="text"></td>
				<td></td>
			</tr>
			<tr>
				<th><label>{t}label{/t}</label></th>
				<td><input type="text" /></td>
				<th><label>inverse label</label></th>
				<td><input type="text"></td>
			</tr>
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<ol style="list-style:decimal;">
						<li style="margin:0 0 2px 20px; float:left"><input placeholder="{t}insert a new params{/t}" type="text" name="newparam" /></li>
					</ol>
				</td>
			</tr>
		</table>

		<input type="submit" style="margin:10px 10px 10px 70px;" value="{t}save this new relation{/t}" />
	</form>

{foreach from=$conf->objRelationType item=item key=keyname}

	<div class="tab"><h2>{$keyname}</h2></div>
	
	<form id="{$keyname}">		
		<table class="bordered">
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
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<ol style="list-style:decimal;">
				{if !empty($item.params)}
					{foreach name=p from=$item.params item=param key=k}
						<li style="margin:0 0 2px 20px; float:left"><input type="text" name="param" value="{$param}" /></li>
					{/foreach}
				{/if}
						<li style="margin:0 0 2px 20px; float:left"><input placeholder="{t}insert a new params{/t}" type="text" name="newparam" /></li>
					</ol>
				</td>
			</tr>
		</table>

		<input type="submit" style="margin:10px 10px 10px 70px;" value="{t}save '{$keyname}' relation{/t}" />
	</form>
{/foreach}


</div>