<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:02
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_geotag.tpl" */ ?>
<?php /*%%SmartyHeaderCode:672019948504ef6daa640b2-54660767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b7a2f7ee94e3d7d2d4eb5ff06a36c4dc5dbcff5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_geotag.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '672019948504ef6daa640b2-54660767',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6dabbac85_08775772',
  'variables' => 
  array (
    'title' => 0,
    'object' => 0,
    'd' => 0,
    'conf' => 0,
    'value' => 0,
    'label' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6dabbac85_08775772')) {function content_504ef6dabbac85_08775772($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><!-- Google Maps API key API 3.3 -->
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>

<script>
$(document).ready(function(){
	
	$("A.googlemaptest").css("cursor","pointer").click(function(){
		if ( ( $(".lat").val() == "" ) ) {
			alert ("you need the latitude value"); return;
		} 
		if ( ( $(".lng").val() == "" ) ) {
			alert ("you need the longitude value"); return;
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
		window.open("http://maps.google.com/maps?"+q);
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

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['title']->value)===null||$tmp==='' ? 'Geotag' : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="geotag">

<?php if (isset($_smarty_tpl->tpl_vars['object']->value['GeoTag'][0])){?>	
<?php $_smarty_tpl->tpl_vars['d'] = new Smarty_variable($_smarty_tpl->tpl_vars['object']->value['GeoTag'][0], null, 0);?>
<?php }?>

<table>
<tr>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<td colspan=3><input type="text" class="geoaddress" style="width:100%;" name="data[GeoTag][0][address]" value="<?php if (!empty($_smarty_tpl->tpl_vars['d']->value['address'])){?><?php echo $_smarty_tpl->tpl_vars['d']->value['address'];?>
<?php }?>"></td>
</tr>
<tr>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
latitude<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<td><input class="lat"  type="text" style="width:140px;" name="data[GeoTag][0][latitude]" value="<?php if (!empty($_smarty_tpl->tpl_vars['d']->value['latitude'])){?><?php echo $_smarty_tpl->tpl_vars['d']->value['latitude'];?>
<?php }?>"></td>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
longitude<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<td><input class="lng" type="text" style="width:140px;" name="data[GeoTag][0][longitude]" value="<?php if (!empty($_smarty_tpl->tpl_vars['d']->value['longitude'])){?><?php echo $_smarty_tpl->tpl_vars['d']->value['longitude'];?>
<?php }?>"></td>
</tr>
<tr>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
map zoom<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<td>
		<select id="mapZoom" name="data[GeoTag][0][gmaps_lookat][zoom]">
			<option></option>
			<?php  $_smarty_tpl->tpl_vars["label"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["label"]->_loop = false;
 $_smarty_tpl->tpl_vars["value"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->geoTagOptions['zoom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["label"]->key => $_smarty_tpl->tpl_vars["label"]->value){
$_smarty_tpl->tpl_vars["label"]->_loop = true;
 $_smarty_tpl->tpl_vars["value"]->value = $_smarty_tpl->tpl_vars["label"]->key;
?>
				<option <?php if ((($tmp = @$_smarty_tpl->tpl_vars['d']->value['gmaps_lookat']['zoom'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['value']->value){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['label']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<?php } ?>
		</select>
	</td>
	<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
map type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<td>
		<select id="mapType" name="data[GeoTag][0][gmaps_lookat][mapType]">
			<option></option>
			<?php  $_smarty_tpl->tpl_vars["label"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["label"]->_loop = false;
 $_smarty_tpl->tpl_vars["value"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->geoTagOptions['mapType']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["label"]->key => $_smarty_tpl->tpl_vars["label"]->value){
$_smarty_tpl->tpl_vars["label"]->_loop = true;
 $_smarty_tpl->tpl_vars["value"]->value = $_smarty_tpl->tpl_vars["label"]->key;
?>
				<option <?php if ((($tmp = @$_smarty_tpl->tpl_vars['d']->value['gmaps_lookat']['mapType'])===null||$tmp==='' ? '' : $tmp)==$_smarty_tpl->tpl_vars['value']->value){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['label']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td></td>
	<td colspan="3"><input type="button" class="geocodeme" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Find and fill latlong coords<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /> <a target="_blank" class="BEbutton googlemaptest"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Test on GoogleMaps<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
</tr>

</table>


</fieldset>
<?php }} ?>