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


	
	<table class="indexlist">

		<tr>
			<th>{t}name{/t}</th>
			<th>{t}area{/t}</th>
			<th>{t}status{/t}</th>
			<th colspan="2">&nbsp;</th>
			<th>Id</th>
		</tr>

			
		{foreach from=$categories item="cat" name="fc"}
		<form id="form_{$cat.id}" method="post" action="{$html->url('saveCategories')}">

			<tr>

				<td style="width:180px">
					<input type="text" name="data[label]" value="{$cat.label}" class="{literal}{required:true}{/literal}"/>
				</td>
				<td>
					<select name="data[area_id]">

						<option value="">--</option>
						{foreach from=$areasList key="area_id" item="public_name"}
						<option value="{$area_id}"{if $area_id == $cat.area_id} selected{/if}>{$public_name}</option>
						{/foreach}
						
					</select>
				</td>
				<td>
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
				<td>{$cat.id}</td>
			</tr>
			
			</form>
		{foreachelse}
		
			<tr><td colspan="5">{t}No categories found{/t}</td></tr>
		
		{/foreach}
		
		</table>
		
		
		
		
		
		<div class="tab"><h2>{t}Add new category{/t}</h2></div>
		
		
		
		<form method="post" id="addCat" action="{$html->url('saveCategories')}">
		
		<table class="indexlist">
			<tr>
				<th>{t}name{/t}</th>
				<th>{t}area{/t}</th>
				<th>{t}status{/t}</th>
				<th></th>
			</tr>
			<tr>
				<td style="width:180px"><input type="text" name="data[label]" value=""/></td>
				<td>
						<select name="data[area_id]">
							<option value="">--</option>
							{foreach from=$areasList key="area_id" item="public_name"}
							<option value="{$area_id}">{$public_name}</option>
							{/foreach}
						</select>
				</td>
				<td nowrap>
					<input type="radio" name="data[status]" value="on" checked="true"/> on
					<input type="radio" name="data[status]" value="off"/> off
				</td>
				<td>
					<input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
					<input type="submit" value="{t}Save{/t}" />
				</td>
			</tr>
		</table>
		
		</form>
	