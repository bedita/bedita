{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}

<script type="text/javascript">
var urlDelete = "{$html->url('deleteCustomProperties/')}";
var message = "{t}Are you sure that you want to delete the property? this will also delete any properties data associated{/t}";

$(document).ready(function() {
	$(".delete").bind("click", function() {
		if (!confirm(message)) {
			return false;
		}
		var customId = $(this).prop("title");
		$("#form_" + customId).prop("action", urlDelete).submit();
		return false;
	});


    $(".optionlist option[value!='options']:selected").parents("tr").find(".optionsfields").hide();

	$(".optionlist").change(function () {
        $(".optionsfields").hide();
		$(".optionlist option[value='options']:selected").parents("tr").find(".optionsfields").show();
    })

});
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="customproperties"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2>{t}Custom properties{/t}</h2>
	
	{*include file="./inc/toolbar.tpl" label_items='custom properties'*}
	</div>
</div>


{include file="inc/menucommands.tpl" method="customproperties" fixed=false}

<div class="mainfull">

	<table class="indexlist">
		<tr>
			<th>{t}property name{/t}</th>
			<th>{t}type{/t}</th>
			<th>{t}data type{/t}</th>
			<th>{t}option list{/t}</th>
			<th>{t}data attributes{/t}</th>
			<th></th>
		</tr>
		{foreach from=$properties item="p"}
			<form id="form_{$p.id}" action="{$html->url('/admin/saveCustomProperties')}" method="post">
			{$beForm->csrf()}
			<tr name="pop">

				<td>
					<input type="text" name="data[Property][name]" value="{$p.name|escape}" />
				</td>
				<td style="vertical-align:middle">
                    {if !empty($p.object_type_id)}
                        {$typeName=$conf->objectTypes[$p.object_type_id].name}
                    {else}
                        {$typeName='user'}
                    {/if}
					
					<span class="listrecent {$typeName}">&nbsp;&nbsp;</span>
					<input type="hidden" name="data[Property][object_type_id]" value="{$p.object_type_id|default:null}"/>
					{$typeName}
					
				</td>
				<td>
					<select class="optionlist" name="data[Property][property_type]">
						<option value="number" {if $p.property_type == "number"} selected="selected"{/if}>{t}number{/t}</option>
						<option value="date" {if $p.property_type == "date"} selected="selected"{/if}>{t}date{/t}</option>
						<option value="text" {if $p.property_type == "text"} selected="selected"{/if}>{t}text{/t}</option>
						<option value="options" {if $p.property_type == "options"} selected="selected"{/if}>{t}options{/t}</option>
					</select>
				</td>
				
				<td>
					{assign var="options" value=""}
					{if !empty($p.PropertyOption)}
						{foreach from=$p.PropertyOption item="opt" name="fcopt"}
							{if $smarty.foreach.fcopt.iteration == 1}
								{assign var="options" value=$opt.property_option}
							{else}
								{assign_concat var="options" 1=$options 2="," 3=$opt.property_option}
							{/if}
						{/foreach}
					{/if}
					<input class="optionsfields" ctype="text" value="{$options|escape}" name="data[options]" />
				</td>
	 			<td>
	 				<span class="optionsfields">
					<input type="checkbox" name="data[Property][multiple_choice]" value="1"{if $p.multiple_choice == 1} checked="checked"{/if} /> {t}multiple choice{/t}
					</span>
				</td>
				
				
				<td>
					<input type="hidden" name="data[Property][id]" value="{$p.id}"/>
					<input type="submit" value=" {t}save{/t} "/>
					<input type="button" class="delete" title="{$p.id}" value="{t}delete{/t}"/>
					
				</td>
			</tr>
			
			</form>
		{foreachelse}
			<tr><td>{t}no custom properties defined{/t}</td></tr>
		{/foreach}
	</table>

	
	<form action="{$html->url('/admin/saveCustomProperties')}" method="post">
	{$beForm->csrf()}
	<h2 style="margin:30px 0 15px 0" >{t}Create a new custom property{/t}</h2>
	<table class="indexlist">
		<tr>
			<th>{t}property name{/t}</th>
			<th>{t}type{/t}</th>
			<th>{t}data type{/t}</th>
			<th>{t}option list{/t}</th>
			<th>{t}data attributes{/t}</th>
			<th></th>
		</tr>
		
		<tr id="row_0">
			<td>
				<input type="text" value="" name="data[Property][name]" />
			</td>
			<td>
				<select name="data[Property][object_type_id]">
				<option value="" class="admin" style="padding-left:5px"> user</option>
				{foreach from=$conf->objectTypes key="key" item="objectTypes"}
				{if !empty($objectTypes.model) && is_numeric($key)}
					<option value="{$objectTypes.id}" class="{$objectTypes.module_name}" style="padding-left:5px"> {$objectTypes.name}</option>
				{/if}
				{/foreach}
				</select>
				
			</td>
			<td>
				<select class="optionList" name="data[Property][property_type]">
					<option value="number">{t}number{/t}</option>
					<option value="date">{t}date{/t}</option>
					<option value="text">{t}text{/t}</option>
					<option value="options">{t}options{/t}</option>
				</select>
			</td>
			<td >
				<input class="optionsfields" ctype="text" value="" name="data[options]" />
			</td>
 			<td><span class="optionsfields">
				<input type="checkbox" name="data[Property][multiple_choice]" value="1" /> {t}multiple choice{/t}
				</span>
			</td>
			<td style="width:110px" >
				<input style="width:100%" type="submit" value="{t}save{/t}" />
			</td>
		</tr>
			
	</table>
	<br /><br />
	</form>
</div>

