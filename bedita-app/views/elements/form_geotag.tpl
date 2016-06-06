<!-- Google Maps API key API 3.3 -->
<script type="text/javascript"
    src="https://maps.google.com/maps/api/js?sensor=false">
</script>

<script>
$(document).ready(function(){
	
    $('.googlemaptest').css('cursor', 'pointer').click(function(){
		if ( ( $(".lat").val() == "" ) ) {
			alert ("latitude value is required"); return;
		} 
		if ( ( $(".lng").val() == "" ) ) {
			alert ("longitude value is required}"); return;
		}
		
		var latitude = $.trim($(".lat").val());
		var longitude = $.trim($(".lng").val());
		var q = "q="+ latitude +","+ longitude + "&z=" + $("#mapZoom").val();
		var mapType = $("#mapType").val();
		if (mapType == "c") {
			q += "&layer=" +  $("#mapType").val() + "&cbll=" + latitude +","+ longitude + "&cbp=12";
		} else {
			q += "&t=" +  $("#mapType").val();
		}
		window.open("https://maps.google.com/maps?" + q + "&output=classic");
	});	
	
    try {
        geocoder = new google.maps.Geocoder();
        $('.geocodeme').click(function() {
            var address = $('.geoaddress').val();
            if (address == '') {
                alert('devi prima inserire un indirizzo'); return;
            }
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latlng = '' + results[0].geometry.location + '';
                    var latlng = latlng.replace('(', '').replace(')', '');
                    var sublatlng = latlng.split(',');
                    $('.lat').val(sublatlng[0]);
                    $('.lng').val(sublatlng[1]);
                    $('.latlong').val(latlng)
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        });
    } catch (err) {
        $('.geocodeme, .googlemaptest').attr('disabled', 'disabled');
        console.warn('Google CDN unreachable. Some functionalities have been disabled.');
    }
});
</script>

{$relcount = $object.GeoTag|@count|default:0}

<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}GeoTag{/t}</h2></div>

<fieldset id="geotag">

{if isset($object.GeoTag.0)}	
{assign var=d value=$object.GeoTag.0}
{/if}

<table>
<tr>
    <th>{t}title{/t}:</th>
    <td colspan=3><input type="text" style="width:100%;" name="data[GeoTag][0][title]" value="{if !empty($d.title)}{$d.title}{/if}"></td>
</tr>
<tr>
	<th>{t}address{/t}:</th>
	<td colspan=3><input type="text" class="geoaddress" style="width:100%;" name="data[GeoTag][0][address]" value="{if !empty($d.address)}{$d.address}{/if}"></td>
</tr>
<tr>
	<th>{t}latitude{/t}:</th>
	<td><input class="lat"  type="text" style="width:140px;" name="data[GeoTag][0][latitude]" value="{if !empty($d.latitude)}{$d.latitude}{/if}"></td>
	<th>{t}longitude{/t}:</th>
	<td><input class="lng" type="text" style="width:140px;" name="data[GeoTag][0][longitude]" value="{if !empty($d.longitude)}{$d.longitude}{/if}"></td>
</tr>
<tr>
	<th>{t}map zoom{/t}:</th>
	<td>
		<select id="mapZoom" name="data[GeoTag][0][gmaps_lookat][zoom]">
			<option></option>
			{foreach from=$conf->geoTagOptions.zoom key="value" item="label"}
				<option {if $d.gmaps_lookat.zoom|default:"" == $value}selected="selected"{/if} value="{$value}">{t}{$label}{/t}</option>
			{/foreach}
		</select>
	</td>
	<th>{t}map type{/t}:</th>
	<td>
		<select id="mapType" name="data[GeoTag][0][gmaps_lookat][mapType]">{* m" map, "k" satellite, "h" hybrid, "p" terrain, "e" GoogleEarth *}
			<option></option>
			{foreach from=$conf->geoTagOptions.mapType key="value" item="label"}
				<option {if $d.gmaps_lookat.mapType|default:"" == $value}selected="selected"{/if} value="{$value}">{t}{$label}{/t}</option>
			{/foreach}
		</select>
	</td>
</tr>
{*
<tr>
	<th>{t}Gmaps LookaT{/t}:</th>
	<td colspan=3><textarea name="data[GeoTag][0][gmaps_lookat]" class="autogrowarea" style="height:16px; width:300px;">{if !empty($d.gmaps_lookat)}{$d.gmaps_lookat}{/if}</textarea></td>
</tr>
*}
<tr>
	<td></td>
	<td colspan="3"><input type="button" class="geocodeme" value="{t}Find and fill latlong coords{/t}" /> <input type="button" class="googlemaptest" value="{t}Test on GoogleMaps{/t}" /></td>
</tr>

</table>


</fieldset>
