<script type="text/javascript">
var urlDelete = "{$html->url('deleteMailGroups/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";
{literal}
$(document).ready(function(){
	$(".delete").bind("click", function(){
		if(!confirm(message)) return false ;
		var groupId = $(this).attr("title");
		$("#form_"+groupId).attr("action", urlDelete).submit();
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
			<th>{t}visible{/t}</th>
			<th>{t}publishing{/t}</th>
			<th>Id</th>
			<th>&nbsp;</th>
		</tr>

		{foreach from=$mailGroups item="grp" name="fc"}
		<form id="form_{$grp.id}" method="post" action="{$html->url('saveMailGroups')}">

			<tr>
				<td>
					<input type="text" style="width:220px" name="data[group_name]" value="{$grp.group_name}" class="{literal}{required:true}{/literal}"/>
				</td>
				<td>
					<input type="radio" name="data[visible]" value="1" {if $grp.visible == "1"}checked="true"{/if}/>on
					&nbsp;
					<input type="radio" name="data[visible]" value="0" {if $grp.visible == "0"}checked="true"{/if}/>off
				</td>
				<td>
					<select style="width:180px" name="data[area_id]">
						{foreach from=$areasList key="area_id" item="public_name"}
							<option value="{$area_id}"{if $area_id == $grp.area_id} selected{/if}>{$public_name}</option>
						{/foreach}
					</select>
				</td>
				<td>{$grp.id}</td>
				<td>
					<input type="hidden" name="data[id]" value="{$grp.id}"/>
					<input type="submit" value=" {t}save{/t} "/>
					<input type="button" class="delete" title="{$grp.id}" value="{t}delete{/t}"/>
				</td>
			</tr>
			
		</form>
		{foreachelse}
		
			<tr><td colspan="5">{t}No mail group found{/t}</td></tr>
		
		{/foreach}
		
		</table>

		<br />

		<div class="tab"><h2>{t}Add new mail group{/t}</h2></div>

		<form method="post" id="addGrp" action="{$html->url('saveMailGroups')}">

		<table class="indexlist">
			<tr>
				<th>{t}name{/t}</th>
				<th>{t}visible{/t}</th>
				<th>{t}publishing{/t}</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><input type="text" style="width:220px" name="data[group_name]" value="" /></td>
				<td>
					<input type="radio" name="data[visible]" value="1" checked="true"/>on
					&nbsp;
					<input type="radio" name="data[visible]" value="0"/>off
				</td>
				<td>
					<select style="width:180px" name="data[area_id]">
						{foreach from=$areasList key="area_id" item="public_name"}
						<option value="{$area_id}">{$public_name}</option>
						{/foreach}
					</select>
				</td>
				<td style="width:140px; text-align:right">
					<input type="submit" style="width:120px" value=" {t}save{/t} " />
				</td>
			</tr>
		</table>

		</form>	