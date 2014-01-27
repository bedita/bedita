<!-- https://github.com/vicb/bsmSelect -->

{$html->script("libs/jquery.bsmselect")}
{$html->css('jquery.bsmselect.css')}

<script type="text/javascript">
	var messageDel = "{t}Do you want to remove the relation? Relation already created between objects will be maintained.{/t}"
	var urlDelete = "{$html->url('/admin/deleteCustomRelation')}";
    $(document).ready(function() {
		openAtStart("table[id]");

		$("select[multiple]").bsmSelect();

		$("input.js-del-relation").click(function() {
			if (!confirm(messageDel)) {
				return false ;
			}
			var customId = $(this).attr("title");
			$(this).parents("form:first").attr("action", urlDelete).submit();
			return false;
		});
    });
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Manage custom relations{/t}</h1>
</div>

<div class="main">

	<div class="tab"><h2>{t}Create a new custom relation{/t}</h2></div>

	<form method="post" action="{$html->url('/admin/saveCustomRelation')}">
		<table class="bordered">
			<tr>
				<th style="vertical-align:top"><label>source</label></th>
				<td style="vertical-align:top">
					<select multiple name="data[left][]">
						<option value="related" selected="selected">all</option>
					{foreach $conf->objectTypes.related.id as $id}	
						<option value="{$conf->objectTypes[$id].name}">	
							{t}{$conf->objectTypes[$id].name}{/t}
						</option>
					{/foreach}
					</select>
				</td>
				<th style="vertical-align:top">
					&nbsp;&nbsp;→&nbsp;&nbsp; <label>target</label>
				</th>
				<td style="vertical-align:top">
					<select multiple name="data[right][]">
						<option value="related" selected="selected">all</option>
					{foreach $conf->objectTypes.related.id as $id}	
						<option value="{$conf->objectTypes[$id].name}">	
							{t}{$conf->objectTypes[$id].name}{/t}
						</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th><label>{t}name{/t}</label></th>
				<td><input type="text" name="data[name]"></td>
				<th><label>{t}inverse name{/t}</label></th>
				<td><input type="text" name="data[inverse]"></td>
				<td></td>
			</tr>
			<tr>
				<th><label>{t}label{/t}</label></th>
				<td><input type="text"  name="data[label]"/></td>
				<th><label>inverse label</label></th>
				<td><input type="text" name="data[inverseLabel]"></td>
			</tr>
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<ol style="list-style:decimal;">
						<li style="margin:0 0 2px 20px; float:left"><input placeholder="{t}insert a new params{/t}" type="text" name="data[params][]" /></li>
					</ol>
				</td>
			</tr>
			<tr>
				<th><label>{t}hidden{/t}</label></th>
				<td colspan="5">
					<input type="checkbox" name="data[hidden]" />
				</td>
			</tr>
		</table>

		<input type="submit" style="margin:10px 10px 10px 70px;" value="{t}save this new relation{/t}" />
	</form>

{foreach $conf->objRelationType as $keyname => $item}

	<div class="tab"><h2>{$keyname}</h2></div>
	
	<form id="{$keyname}" method="post" action="{$html->url('/admin/saveCustomRelation')}">
		<table class="bordered">
			<tr>
				<th style="vertical-align:top"><label>source</label></th>
				<td style="vertical-align:top">
					<select multiple name="data[left][]">
						<option value="related" {if empty($item.left) && is_array($item.left)}selected=1{/if}>all</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}" {if in_array($conf->objectTypes[$id].name, $item.left)}selected=1{/if}>	
							{t}{$conf->objectTypes[$id].name}{/t}
						</option>
					{/foreach}
					</select>					
				</td>
				<th style="vertical-align:top">
					&nbsp;&nbsp;→&nbsp;&nbsp; <label>target</label>
				</th>
				<td style="vertical-align:top">
					<select multiple name="data[right][]">
						<option value="related" {if empty($item.right) && is_array($item.right)}selected=1{/if}>all</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}" {if in_array($conf->objectTypes[$id].name, $item.right)}selected=1{/if}>	
							{t}{$conf->objectTypes[$id].name}{/t}
						</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th><label>{t}name{/t}</label></th>
				<td>
					{$keyname}
					<input type="hidden" name="data[name]" value="{$keyname}">
				</td>
				<th><label>{t}inverse name{/t}</label></th>
				<td>
					{$item.inverse|default:'-'}
					<input type="hidden" name="data[inverse]" value="{$item.inverse|default:''}">
				</td>
				<td></td>
			</tr>
			<tr>
				<th><label>{t}label{/t}</label></th>
				<td><input type="text" name="data[label]" value="{$item.label|default:''}" /></td>
				<th><label>{t}inverse label{/t}</label></th>
				<td><input type="text" name="data[inverseLabel]" value="{$item.inverseLabel|default:''}"></td>
			</tr>
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<ol style="list-style:decimal;">
				{if !empty($item.params)}
					{foreach name=p from=$item.params item=param key=k}
						<li style="margin:0 0 2px 20px; float:left"><input type="text" name="data[params][]" value="{$param}" /></li>
					{/foreach}
				{/if}
						<li style="margin:0 0 2px 20px; float:left"><input placeholder="{t}insert a new params{/t}" type="text" name="data[params][]" /></li>
					</ol>
				</td>
			</tr>
			<tr>
				<th><label>{t}hidden{/t}</label></th>
				<td colspan="5">
					<input type="checkbox" name="data[hidden]" {if $item.hidden}checked="checked"{/if}/>
				</td>
			</tr>
		</table>

		<input type="submit" style="margin:10px 10px 10px 70px;" value="{t}save '{$keyname}' relation{/t}" />
		<input type="button" class="js-del-relation" style="margin:10px 10px 10px 70px;" value="{t}delete{/t}" />
	</form>
{/foreach}


</div>