<script type="text/javascript">
	var messageDel = "{t}Do you want to remove the relation? Relation already created between objects will be maintained.{/t}"
	var urlDelete = "{$html->url('/admin/deleteCustomRelation')}";
</script>

<style scoped>
.js-params-options {
	width:240px;
}
</style>
{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Manage custom relations{/t}</h1>
</div>

<div class="main" id="customRelationContainer">

	<div class="tab"><h2>{t}Create a new custom relation{/t}</h2></div>

	<form method="post" action="{$html->url('/admin/saveCustomRelation')}">
		{$beForm->csrf()}
		<table class="bordered">
			<tr>
				<th style="vertical-align:top"><label>{t}source{/t}</label></th>
				<td style="vertical-align:top">
					<select multiple name="data[left][]" data-placeholder="{t}select an object type{/t}">
						<option value="related" selected="selected">{t}all{/t}</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}">{t}{$conf->objectTypes[$id].name}{/t}</option>
					{/foreach}
					</select>
				</td>
				<th style="vertical-align:top">
					&nbsp;&nbsp;→&nbsp;&nbsp; <label>{t}target{/t}</label>
				</th>
				<td style="vertical-align:top">
					<select multiple name="data[right][]" data-placeholder="{t}select an object type{/t}">
						<option value="related" selected="selected">{t}all{/t}</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}">{t}{$conf->objectTypes[$id].name}{/t}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th><label>{t}name{/t}</label></th>
				<td><input type="text" name="data[name]"></td>
				<th><label>{t}inverse name{/t}</label></th>
				<td><input type="text" name="data[inverse]"></td>

			</tr>
			<tr>
				<th><label>{t}label{/t}</label></th>
				<td><input type="text"  name="data[label]"/></td>
				<th><label>inverse label</label></th>
				<td><input type="text" name="data[inverseLabel]"></td>
			</tr>
			<tr>
				<th><label>{t}hidden{/t}</label></th>
				<td colspan="5">
					<input type="checkbox" name="data[hidden]" />
				</td>
			</tr>
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<table class="noborder">
						<tr>
							<td>
								<input placeholder="{t}insert a new params{/t}" type="text" name="data[params][0][name]" />
							</td>
							<th>
								<label>{t}type{/t}</label>
							</th>
							<td>
								<select class="js-params-type" name="data[params][0][type]">
									<option value="text">{t}text{/t}</option>
									<option value="options">{t}options{/t}</option>
								</select>
							</td>
							<th>
								<label>{t}options{/t} *</label>
							</th>
							<td>
								<input type="text" class="js-params-options" name="data[params][0][options]" value="" placeholder="{t}option{/t} 1,{t}option{/t} 2,..."/>
							</td>

						</tr>
						<tr><td colspan="5">* {t}comma separated values{/t}</td></tr>
					</table>
				</td>
			</tr>
		</table>

		<input type="submit" style="margin:10px 10px 10px 70px;" value="{t}save this new relation{/t}" />
	</form>

{foreach $conf->objRelationType as $keyname => $item}

	<div class="tab"><h2>{$keyname|escape}</h2></div>

	<form id="{$keyname}" method="post" action="{$html->url('/admin/saveCustomRelation')}">
		{$beForm->csrf()}
		<table class="bordered">
			<tr>
				<th style="vertical-align:top"><label>{t}source{/t}</label></th>
				<td style="vertical-align:top">
					<select multiple name="data[left][]" data-placeholder="{t}select an object type{/t}">
						<option value="related" {if empty($item.left) && is_array($item.left)}selected=1{/if}>all</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}" {if in_array($conf->objectTypes[$id].name, $item.left)}selected=1{/if}>{strip}
							{t}{$conf->objectTypes[$id].name}{/t}
						{/strip}
						</option>
					{/foreach}
					</select>
				</td>
				<th style="vertical-align:top">
					&nbsp;&nbsp;→&nbsp;&nbsp; <label>{t}target{/t}</label>
				</th>
				<td style="vertical-align:top">
					<select multiple name="data[right][]" data-placeholder="{t}select an object type{/t}">
						<option value="related" {if empty($item.right) && is_array($item.right)}selected=1{/if}>{t}all{/t}</option>
					{foreach $conf->objectTypes.related.id as $id}
						<option value="{$conf->objectTypes[$id].name}" {if in_array($conf->objectTypes[$id].name, $item.right)}selected=1{/if}>{strip}
							{t}{$conf->objectTypes[$id].name}{/t}
						{/strip}
						</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th><label>{t}name{/t}</label></th>
				<td>
					{$keyname}
					<input type="hidden" name="data[name]" value="{$keyname|escape}">
				</td>
				<th><label>{t}inverse name{/t}</label></th>
				<td>
					{$item.inverse|default:'-'}
					<input type="hidden" name="data[inverse]" value="{$item.inverse|default:''|escape}">
				</td>
			</tr>
			<tr>
				<th><label>{t}label{/t}</label></th>
				<td><input type="text" name="data[label]" value="{$item.label|default:''|escape}" /></td>
				<th><label>{t}inverse label{/t}</label></th>
				<td><input type="text" name="data[inverseLabel]" value="{$item.inverseLabel|default:''|escape}"></td>
			</tr>
			<tr>
				<th><label>{t}hidden{/t}</label></th>
				<td colspan="5">
					<input type="checkbox" name="data[hidden]" {if $item.hidden}checked="checked"{/if}/>
				</td>
			</tr>
			<tr>
				<th><label>{t}params{/t}</label></th>
				<td colspan="5">
					<table class="noborder">
						{$paramsIndex = -1}
						{if !empty($item.params)}
							{foreach $item.params as $k => $param}
								{$paramsIndex = $paramsIndex + 1}
								{if is_array($param)}
									{$options = implode(',', $param)}
									{$type = 'options'}
									{$name = $k}
								{else}
									{$type = 'text'}
									{$name = $param}
								{/if}
								<tr>
									<td>
										<input type="text" name="data[params][{$paramsIndex}][name]" value="{$name|escape}" />
									</td>
									<th>
										<label>{t}type{/t}</label>
									</th>
									<td>
										<select class="js-params-type" name="data[params][{$paramsIndex}][type]">
											<option value="text">{t}text{/t}</option>
											<option value="options" {if $type == 'options'}selected{/if}>{t}options{/t}</option>
										</select>
									</td>
									<th>
										<label>{t}options{/t} *</label>
									</th>
									<td>
										<input type="text" class="js-params-options" name="data[params][{$paramsIndex}][options]" value="{if $type == 'options'}{$options|escape}{/if}" placeholder="{t}option{/t} 1,{t}option{/t} 2,..." />
									</td>
								</tr>
							{/foreach}
						{/if}

						{$paramsIndex = $paramsIndex + 1}
						<tr>
							<td>
								<input placeholder="{t}insert a new params{/t}" type="text" name="data[params][{$paramsIndex}][name]" />
							</td>
							<th>
								<label>{t}type{/t}</label>
							</th>
							<td>
								<select class="js-params-type" name="data[params][{$paramsIndex}][type]">
									<option value="text">{t}text{/t}</option>
									<option value="options">{t}options{/t}</option>
								</select>
							</td>
							<th>
								<label>{t}options{/t} *</label>
							</th>
							<td>
								<input type="text" class="js-params-options" name="data[params][{$paramsIndex}][options]" value="" placeholder="{t}option{/t} 1,{t}option{/t} 2,..."/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div style="text-align:right">* {t}comma separated values{/t}</div>

		<input type="submit" style="margin:0px 10px 10px 70px;" value="{t}save{/t} '{$keyname}' {t}relation{/t}" />
		<input type="button" class="js-del-relation" style="margin:0px 10px 20px 70px;" value=" {t}delete{/t} '{$keyname|escape}' {t}relation{/t}" />
	</form>
{/foreach}


</div>