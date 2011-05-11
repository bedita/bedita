{if !empty($conf->googleApi.googleMaps.key)}
{literal}
<!-- Google Maps API key API 3.3 -->
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false&amp;key={/literal}{$conf->googleApi.googleMaps.key}{literal}">
</script>

<script>
$(document).ready(function(){
	
	$("A.googlemaptest").css("cursor","pointer").click(function(){
		var q = $(".lat").val()+","+$(".lng").val();
		alert(q);
		if (q == ""){
			alert ("devi prima inserire lat e long"); return;
		}
		window.open("http://maps.google.com/maps?q="+q+"")
	});	
	
	geocoder = new google.maps.Geocoder();
	$(".geocodeme").click(function(){	
		var address = $(".geoaddress").val();
		if (address == "") {
			alert ("devi prima inserire un indirizzo"); return;
		}
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var latlng = ""+results[0].geometry.location+"";
				var latlng = latlng.replace("(","").replace(")","");				
				var sublatlng = latlng.split(',');
				$(".lat").val(sublatlng[0]);
				$(".lng").val(sublatlng[1]);
				$(".latlong").val(latlng)
				
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});				
	});	
});	
</script>
{/literal}
{/if}
<div class="tab"><h2>{t}{$title|default:'Geotag'}{/t}</h2></div>

<fieldset id="geotag">

{if isset($object.GeoTag.0)}	
{assign var=d value=$object.GeoTag.0}
{/if}

<table>
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
{*
<tr>
	<th>{t}Gmaps LookaT{/t}:</th>
	<td colspan=3><textarea name="data[GeoTag][0][gmaps_lookat]" class="autogrowarea" style="height:16px; width:300px;">{if !empty($d.gmaps_lookat)}{$d.gmaps_lookat}{/if}</textarea></td>
</tr>
*}
<tr>
	<td></td>
	<td colspan="3"><input type="button" class="geocodeme" value="{t}Find and fill latlong coords{/t}" /> <a target="_blank" class="BEbutton googlemaptest">{t}Test on GoogleMaps{/t}</a></td>
</tr>

</table>


</fieldset>
