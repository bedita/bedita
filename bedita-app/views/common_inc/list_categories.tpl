<script type="text/javascript">
var urlDelete = "{$html->url('deleteCategories/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";
{literal}
$(document).ready(function(){
	$(".delete").bind("click", function(){
		if(!confirm(message)) return false ;
		var catId = $(this).attr("title");
		$("#form_"+catId).attr("action", urlDelete).submit();
		return false;
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
			<th>{t}status{/t}</th>
			<th>{t}publication{/t}</th>
			<th>Id</th>
			<th>&nbsp;</th>
		</tr>

			
		{foreach from=$categories item="cat" name="fc"}
		<form id="form_{$cat.id}" method="post" action="{$html->url('saveCategories')}">

			<tr>

				<td>
					<input type="text" style="width:220px" name="data[label]" value="{$cat.label}" class="{literal}{required:true}{/literal}"/>
				</td>
				<td>
				
					<input type="radio" name="data[status]" value="on" {if $cat.status == "on"}checked="true"{/if}/>on
					&nbsp;
					<input type="radio" name="data[status]" value="off" {if $cat.status == "off"}checked="true"{/if}/>off
					
				</td>
				<td>
					<select style="width:180px" name="data[area_id]">

						<option value="">--</option>
						{foreach from=$areasList key="area_id" item="public_name"}
							<option value="{$area_id}"{if $area_id == $cat.area_id} selected{/if}>{$public_name}</option>
						{/foreach}
	
					</select>
				</td>
				<td>{$cat.id}</td>
				<td>
					<input type="hidden" name="data[id]" value="{$cat.id}"/>
					<input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
					<input type="submit" value=" {t}save{/t} "/>
					<input type="button" class="delete" title="{$cat.id}" value="{t}delete{/t}"/>
					
				</td>
			</tr>
			
			</form>
		{foreachelse}
		
			<tr><td colspan="5">{t}No categories found{/t}</td></tr>
		
		{/foreach}
		
		</table>
		
		
		
		<br />
		
		<div class="tab"><h2>{t}Add new category{/t}</h2></div>
		
		
		
		<form method="post" id="addCat" action="{$html->url('saveCategories')}">
		
		<table class="indexlist">
			<tr>
			<th>{t}name{/t}</th>
			<th>{t}status{/t}</th>
			<th>{t}publication{/t}</th>
			<th>&nbsp;</th>
			</tr>
			<tr>
				<td><input type="text" style="width:220px" name="data[label]" value="" /></td>
				<td>
					<input type="radio" name="data[status]" value="on" checked="true"/>on
					&nbsp;
					<input type="radio" name="data[status]" value="off"/>off
				</td>
				<td>
						<select style="width:180px" name="data[area_id]">
							<option value="">--</option>
							{foreach from=$areasList key="area_id" item="public_name"}
							<option value="{$area_id}">{$public_name}</option>
							{/foreach}
						</select>
				</td>
				<td style="width:140px; text-align:right">
					<input type="hidden" name="data[object_type_id]" value="{$object_type_id}"/>
					<input type="submit" style="width:120px" value=" {t}save{/t} " />
				</td>
			</tr>
		</table>
		
		</form>
	