<script type="text/javascript">
var urlDelete = "{$html->url('deleteCategories/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";
{literal}
$(document).ready(function(){
	$("a.delete").bind("click", function(){
		if(!confirm(message)) return false ;
		$(this).parent().siblings("form").attr("action", urlDelete) ;
		$(this).parent().siblings("form").get(0).submit() ;
		return false ;
	});

	$("input[@type=text]").bind("keyup", function(){
		var text = $(this).val();
		if (jQuery.trim(text) == "") {
	   		$(this).parent().siblings().find("input[@type=submit]").attr("disabled", "disabled");
		} else {
	   		$(this).parent().siblings().find("input[@type=submit]").attr("disabled", "");
	    }
	});
	
});
{/literal}
</script>
<div id="containerPage">
	<div id="listElements">
		<table class="indexList">
		<thead>
		<tr>
			<th>Id</th>
			<th>{t}name{/t}</th>
			<th>{t}area{/t}</th>
			<th>{t}status{/t}</th>
			<th colspan="2">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		{if $categories}
			{foreach from=$categories item="cat" name="fc"}
			<tr class="rowList">
				<form id="form_{$cat.id}" method="post" action="{$html->url('saveCategories')}">
				<fieldset>
				<td class="cellList">{$cat.id}</td>
				<td class="cellList">
					<input type="text" name="data[label]" value="{$cat.label}" class="{literal}{required:true}{/literal}"/>
				</td>
				<td class="cellList">
					<select name="data[area_id]">
						<option value=""></option>
						{foreach from=$areasList key="area_id" item="public_name"}
						<option value="{$area_id}"{if $area_id == $cat.area_id} selected{/if}>{$public_name}</option>
						{/foreach}
					</select>
				</td>
				<td class="cellList">
					{strip}
					<input type="radio" name="data[status]" value="on"
						{if $cat.status == "on"}checked="true"{/if}/> on
					<input type="radio" name="data[status]" value="off"
						{if $cat.status == "off"}checked="true"{/if}/> off
					{/strip}
				</td>
				<td>
					<input type="hidden" name="data[id]" value="{$cat.id}"/>
					<input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
					<input type="submit" value="{t}Save{/t}"/>
				</td>
				<td><a href="javascript:void(0);" class="delete" title="{$cat.id}">{t}Delete{/t}</a></td>
				</fieldset>
				</form>
			</tr>
			{/foreach}
		{else}
			<tr><td colspan="5">{t}No categories found{/t}</td></tr>
		{/if}
		<tr><th colspan="5">{t}Add new{/t}</th></tr>
		<tr class="rowList">
			<td>&nbsp;</td>
			<form method="post" id="addCat" action="{$html->url('saveCategories')}">
			<fieldset>
			<td class="cellList"><input type="text" name="data[label]" value=""/></td>
			<td class="cellList">
					<select name="data[area_id]">
						<option value=""></option>
						{foreach from=$areasList key="area_id" item="public_name"}
						<option value="{$area_id}">{$public_name}</option>
						{/foreach}
					</select>
				</td>
			<td class="cellList" nowrap>
				<input type="radio" name="data[status]" value="on" checked="true"/> on
				<input type="radio" name="data[status]" value="off"/> off
			</td>
			<td colspan="2">
				<input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
				<input type="submit" value="{t}Save{/t}" disabled="disabled"/>
			</td>
			</fieldset>
			</form>
		</tr>
		</tbody>
		</table>
	</div>
</div>
</div>