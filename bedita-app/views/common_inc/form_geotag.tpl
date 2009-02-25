
<div class="tab"><h2>{t}Geotag{/t}</h2></div>

<fieldset id="geotag">

{if isset($object.GeoTag.0)}	
{assign var=d value=$object.GeoTag.0}
{/if}

<table>
<tr>
	<th>{t}address{/t}:</th>
	<td colspan=3><input type="text" style="width:300px;" name="data[GeoTag][0][address]" value="{if !empty($d.address)}{$d.address}{/if}"></td>
</tr>
<tr>
	<th>{t}latitude{/t}:</th
	<td><input type="text" style="width:100px;" name="data[GeoTag][0][latitude]" value="{if !empty($d.latitude)}{$d.latitude}{/if}"></td>
	<th>{t}longitude{/t}:</th>
	<td><input type="text" style="width:100px;" name="data[GeoTag][0][longitude]" value="{if !empty($d.longitude)}{$d.longitude}{/if}"></td>
</tr>
{*
<tr>
	<th>{t}Gmaps LookaT{/t}:</th>
	<td colspan=3><textarea name="data[GeoTag][0][gmaps_lookat]" class="autogrowarea" style="height:16px; width:300px;">{if !empty($d.gmaps_lookat)}{$d.gmaps_lookat}{/if}</textarea></td>
</tr>
*}
</table>


</fieldset>
