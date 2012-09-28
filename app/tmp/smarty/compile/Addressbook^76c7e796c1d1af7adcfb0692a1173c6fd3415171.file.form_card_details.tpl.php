<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:13
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_card_details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:448046831504ef5e1a2f8a5-83361439%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76c7e796c1d1af7adcfb0692a1173c6fd3415171' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/inc/form_card_details.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '448046831504ef5e1a2f8a5-83361439',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'view' => 0,
    'object' => 0,
    'attach' => 0,
    'params' => 0,
    'beEmbedMedia' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e1dc41e1_14771776',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e1dc41e1_14771776')) {function content_504ef5e1dc41e1_14771776($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?><?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<script type="text/javascript">

$(document).ready(function(){

$("#fototessera").click(function () {
	$('.main .tab').BEtabsclose();
	$('#multimedia').prev('.tab').BEtabstoggle();
});

$(".htab TD").click(function () {
	$("input",this).attr({checked:"checked"});
});

<?php if ((!empty($_smarty_tpl->tpl_vars['object']->value)&&!empty($_smarty_tpl->tpl_vars['object']->value['country']))){?>

$("#country").selectOptions("<?php echo $_smarty_tpl->tpl_vars['object']->value['country'];?>
",true);

<?php }?>

<?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['id'])&&($_smarty_tpl->tpl_vars['object']->value['company']==1))){?>

$(".htab TD[rel:company]").click();

<?php }?>

})


</script>

</script>

<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Card<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="card">

<table class="htab">
	<td id="personh" rel="person">
		<input type="radio" name="data[company]" value="0" <?php if ((empty($_smarty_tpl->tpl_vars['object']->value['id'])||($_smarty_tpl->tpl_vars['object']->value['company']==0))){?>checked="checked"<?php }?> />
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Person<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	<td id="companyh" rel="company">
		<input type="radio" name="data[company]" value="1" <?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['id'])&&($_smarty_tpl->tpl_vars['object']->value['company']==1))){?>checked="checked"<?php }?> />
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Organization<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
</table>

		
<div class="htabcontainer" id="companyperson">
	
	<div class="htabcontent" id="person">
		<table>
			<tr>
			<?php if (!empty($_smarty_tpl->tpl_vars['attach']->value[0])){?>
				<td id="fototessera" style="padding-right:10px;" rowspan="4">
					<?php echo smarty_function_assign_associative(array('var'=>"params",'width'=>100,'height'=>125,'longside'=>false,'mode'=>"crop"),$_smarty_tpl);?>

					<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['attach']->value[0],$_smarty_tpl->tpl_vars['params']->value);?>

				</td>
			<?php }?>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td>
					<input type="text" style="width:45px" id="vtitle" name="data[person][person_title]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['person_title'], ENT_QUOTES, 'UTF-8', true));?>
" />
				</td>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" name="data[person][name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['name'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
			</tr>
			<tr>				
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
surname<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" name="data[person][surname]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['surname'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
			</tr>	
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
organization<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" name="data[person][company_name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['company_name'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>				
			</tr>
			<tr>
				<td colspan="3">
					<input type="radio" name="data[gender]" value="male" <?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['gender'])&&$_smarty_tpl->tpl_vars['object']->value['gender']=='male')){?>checked="checked"<?php }?>/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
male<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 &nbsp&nbsp
					<input type="radio" name="data[gender]" value="female" <?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['gender'])&&$_smarty_tpl->tpl_vars['object']->value['gender']=='female')){?>checked="checked"<?php }?>/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
female<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 &nbsp&nbsp
					<input type="radio" name="data[gender]" value="transgender" <?php if ((!empty($_smarty_tpl->tpl_vars['object']->value['gender'])&&$_smarty_tpl->tpl_vars['object']->value['gender']=='transgender')){?>checked="checked"<?php }?>/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
transgender<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</td>
			</tr>
		</table>
		<table>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
birthdate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" style="width:75px" class="dateinput" name="data[person][birthdate]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['birthdate'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['birthdate'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
<?php }?>"/></td>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
deathdate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" style="width:75px" class="dateinput" name="data[person][deathdate]" value="<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['deathdate'])){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['deathdate'],$_smarty_tpl->tpl_vars['conf']->value->datePattern);?>
<?php }?>"/></td>
			</tr>
		</table>
	</div>



	<div class="htabcontent" id="company" >
		<table>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" style="width:400px" name="data[cmp][company_name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['company_name'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
			</tr>
		</table>
		<table>
			<tr>
				<th colspan=2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" name="data[cmp][name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['name'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>			
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
surname<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td><input type="text" name="data[cmp][surname]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['surname'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
			</tr>
			<tr>
				<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
				<td>
					<input type="text" style="width:45px" id="vtitle" name="data[cmp][person_title]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['person_title'], ENT_QUOTES, 'UTF-8', true));?>
" />
				</td>
			</tr>
		</table>
	</div>
		
</div>

</fieldset>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="address">
<table>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
street name and #<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input style="width:240px;" type="text" name="data[street_address]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['street_address'], ENT_QUOTES, 'UTF-8', true));?>
" />
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
city and zip code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<input type="text" name="data[city]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['city'], ENT_QUOTES, 'UTF-8', true));?>
" />
			<input style="width:60px;" type="text" name="data[zipcode]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['zipcode'], ENT_QUOTES, 'UTF-8', true));?>
" />
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td>
			<select type="text" name="data[country]" id="country">
				<option value="">-- <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select one<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 --</option>
				<option value="Afghanistan">Afghanistan</option>
				<option value="Albania">Albania</option>
				<option value="Algeria">Algeria</option>
				<option value="American Samoa">American Samoa</option>
				<option value="Andorra">Andorra</option>
				<option value="Angola">Angola</option>
				<option value="Anguilla">Anguilla</option>
				<option value="Antigua and Barbuda">Antigua and Barbuda</option>
				<option value="Argentina">Argentina</option>
				<option value="Armenia">Armenia</option>
				<option value="Aruba">Aruba</option>
				<option value="Australia">Australia</option>
				<option value="Austria">Austria</option>
				<option value="Azerbaijan">Azerbaijan</option>
				<option value="Bahamas">Bahamas</option>
				<option value="Bangladesh">Bangladesh</option>
				<option value="Barbados">Barbados</option>
				<option value="Belarus">Belarus</option>
				<option value="Belgium">Belgium</option>
				<option value="Belize">Belize</option>
				<option value="Benin">Benin</option>
				<option value="Bermuda">Bermuda</option>
				<option value="Bhutan">Bhutan</option>
				<option value="Bolivia">Bolivia</option>
				<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
				<option value="Botswana">Botswana</option>
				<option value="Bouvet Island">Bouvet Island</option>
				<option value="Brazil">Brazil</option>
				<option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
				<option value="Brunei Darussalam">Brunei Darussalam</option>
				<option value="Bulgaria">Bulgaria</option>
				<option value="Burkina Faso">Burkina Faso</option>
				<option value="Burundi">Burundi</option>
				<option value="Cambodia">Cambodia</option>
				<option value="Cameroon">Cameroon</option>
				<option value="Canada">Canada</option>
				<option value="Cape Verde">Cape Verde</option>
				<option value="Cayman Islands">Cayman Islands</option>
				<option value="Central African Republic">Central African Republic</option>
				<option value="Chad">Chad</option>
				<option value="Chile">Chile</option>
				<option value="China">China</option>
				<option value="Christmas Island">Christmas Island</option>
				<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
				<option value="Colombia">Colombia</option>
				<option value="Comoros">Comoros</option>
				<option value="Congo">Congo</option>
				<option value="Congo, Democratic Republic of the">Congo, Democratic Republic of the</option>
				<option value="Cook Islands">Cook Islands</option>
				<option value="Costa Rica">Costa Rica</option>
				<option value="Croatia">Croatia</option>
				<option value="Cuba">Cuba</option>
				<option value="Cyprus">Cyprus</option>
				<option value="Czech Republic">Czech Republic</option>
				<option value="Côte d'Ivoire">Côte d'Ivoire</option>
				<option value="Djibouti">Djibouti</option>
				<option value="Dominica">Dominica</option>
				<option value="Dominican Republic">Dominican Republic</option>
				<option value="Ecuador">Ecuador</option>
				<option value="Egypt">Egypt</option>
				<option value="El Salvador">El Salvador</option>
				<option value="Equatorial Guinea">Equatorial Guinea</option>
				<option value="Eritrea">Eritrea</option>
				<option value="Estonia">Estonia</option>
				<option value="Ethiopia">Ethiopia</option>
				<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
				<option value="Faroe Islands">Faroe Islands</option>
				<option value="Fiji">Fiji</option>
				<option value="Finland">Finland</option>
				<option value="France">France</option>
				<option value="French Guiana">French Guiana</option>
				<option value="French Polynesia">French Polynesia</option>
				<option value="French Southern Territories">French Southern Territories</option>
				<option value="Gabon">Gabon</option>
				<option value="Georgia">Georgia</option>
				<option value="Germany">Germany</option>
				<option value="Ghana">Ghana</option>
				<option value="Gibraltar">Gibraltar</option>
				<option value="Greece">Greece</option>
				<option value="Greenland">Greenland</option>
				<option value="Grenada">Grenada</option>
				<option value="Guadeloupe">Guadeloupe</option>
				<option value="Guam">Guam</option>
				<option value="Guatemala">Guatemala</option>
				<option value="Guernsey">Guernsey</option>
				<option value="Guinea">Guinea</option>
				<option value="Guinea-Bissau">Guinea-Bissau</option>
				<option value="Guyana">Guyana</option>
				<option value="Haiti">Haiti</option>
				<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
				<option value="Honduras">Honduras</option>
				<option value="Hong Kong">Hong Kong</option>
				<option value="Hungary">Hungary</option>
				<option value="Iceland">Iceland</option>
				<option value="India">India</option>
				<option value="Indonesia">Indonesia</option>
				<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
				<option value="Iraq">Iraq</option>
				<option value="Ireland">Ireland</option>
				<option value="Isle of Man">Isle of Man</option>
				<option value="Israel">Israel</option>
				<option value="Italy">Italy</option>
				<option value="Japan">Japan</option>
				<option value="Jersey">Jersey</option>
				<option value="Jordan">Jordan</option>
				<option value="Kazakhstan">Kazakhstan</option>
				<option value="Kenya">Kenya</option>
				<option value="Kiribati">Kiribati</option>
				<option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
				<option value="Korea, Republic of">Korea, Republic of</option>
				<option value="Kuwait">Kuwait</option>
				<option value="Kyrgyzstan">Kyrgyzstan</option>
				<option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
				<option value="Latvia">Latvia</option>
				<option value="Lebanon">Lebanon</option>
				<option value="Lesotho">Lesotho</option>
				<option value="Liberia">Liberia</option>
				<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
				<option value="Liechtenstein">Liechtenstein</option>
				<option value="Lithuania">Lithuania</option>
				<option value="Luxembourg">Luxembourg</option>
				<option value="Macedonia, the former Yugoslav Republic of">Macedonia, the former Yugoslav Republic of</option>
				<option value="Madagascar">Madagascar</option>
				<option value="Malawi">Malawi</option>
				<option value="Malaysia">Malaysia</option>
				<option value="Maldives">Maldives</option>
				<option value="Mali">Mali</option>
				<option value="Malta">Malta</option>
				<option value="Marshall Islands">Marshall Islands</option>
				<option value="Martinique">Martinique</option>
				<option value="Mauritania">Mauritania</option>
				<option value="Mauritius">Mauritius</option>
				<option value="Mayotte">Mayotte</option>
				<option value="Mexico">Mexico</option>
				<option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
				<option value="Moldova, Republic of">Moldova, Republic of</option>
				<option value="Monaco">Monaco</option>
				<option value="Mongolia">Mongolia</option>
				<option value="Montenegro">Montenegro</option>
				<option value="Montserrat">Montserrat</option>
				<option value="Morocco">Morocco</option>
				<option value="Mozambique">Mozambique</option>
				<option value="Myanmar">Myanmar</option>
				<option value="Namibia">Namibia</option>
				<option value="Nauru">Nauru</option>
				<option value="Nepal">Nepal</option>
				<option value="Netherlands">Netherlands</option>
				<option value="Netherlands Antilles">Netherlands Antilles</option>
				<option value="New Caledonia">New Caledonia</option>
				<option value="New Zealand">New Zealand</option>
				<option value="Nicaragua">Nicaragua</option>
				<option value="Niger">Niger</option>
				<option value="Nigeria">Nigeria</option>
				<option value="Niue">Niue</option>
				<option value="Norfolk Island">Norfolk Island</option>
				<option value="Northern Mariana Islands">Northern Mariana Islands</option>
				<option value="Norway">Norway</option>
				<option value="Oman">Oman</option>
				<option value="Pakistan">Pakistan</option>
				<option value="Palau">Palau</option>
				<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
				<option value="Panama">Panama</option>
				<option value="Papua New Guinea">Papua New Guinea</option>
				<option value="Paraguay">Paraguay</option>
				<option value="Peru">Peru</option>
				<option value="Philippines">Philippines</option>
				<option value="Pitcairn">Pitcairn</option>
				<option value="Poland">Poland</option>
				<option value="Portugal">Portugal</option>
				<option value="Puerto Rico">Puerto Rico</option>
				<option value="Qatar">Qatar</option>
				<option value="Russian Federation">Russian Federation</option>
				<option value="Rwanda">Rwanda</option>
				<option value="Réunion">Réunion</option>
				<option value="Saint Barthélemy">Saint Barthélemy</option>
				<option value="Saint Helena">Saint Helena</option>
				<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
				<option value="Saint Lucia">Saint Lucia</option>
				<option value="Saint Martin (French part)">Saint Martin (French part)</option>
				<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
				<option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
				<option value="Samoa">Samoa</option>
				<option value="San Marino">San Marino</option>
				<option value="Sao Tome and Principe">Sao Tome and Principe</option>
				<option value="Saudi Arabia">Saudi Arabia</option>
				<option value="Senegal">Senegal</option>
				<option value="Serbia">Serbia</option>
				<option value="Seychelles">Seychelles</option>
				<option value="Sierra Leone">Sierra Leone</option>
				<option value="Singapore">Singapore</option>
				<option value="Slovakia">Slovakia</option>
				<option value="Slovenia">Slovenia</option>
				<option value="Solomon Islands">Solomon Islands</option>
				<option value="Somalia">Somalia</option>
				<option value="South Africa">South Africa</option>
				<option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
				<option value="Spain">Spain</option>
				<option value="Sri Lanka">Sri Lanka</option>
				<option value="Sudan">Sudan</option>
				<option value="Suriname">Suriname</option>
				<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
				<option value="Swaziland">Swaziland</option>
				<option value="Sweden">Sweden</option>
				<option value="Switzerland">Switzerland</option>
				<option value="Syrian Arab Republic">Syrian Arab Republic</option>
				<option value="Taiwan, Province of China">Taiwan, Province of China</option>
				<option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
				<option value="Thailand">Thailand</option>
				<option value="Timor-Leste">Timor-Leste</option>
				<option value="Togo">Togo</option>
				<option value="Tokelau">Tokelau</option>
				<option value="Tonga">Tonga</option>
				<option value="Trinidad and Tobago">Trinidad and Tobago</option>
				<option value="Tunisia">Tunisia</option>
				<option value="Turkey">Turkey</option>
				<option value="Turkmenistan">Turkmenistan</option>
				<option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
				<option value="Tuvalu">Tuvalu</option>
				<option value="Ukraine">Ukraine</option>
				<option value="United Arab Emirates">United Arab Emirates</option>
				<option value="United Kingdom">United Kingdom</option>
				<option value="United States">United States</option>
				<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
				<option value="Uruguay">Uruguay</option>
				<option value="Uzbekistan">Uzbekistan</option>
				<option value="Vanuatu">Vanuatu</option>
				<option value="Venezuela">Venezuela</option>
				<option value="Viet Nam">Viet Nam</option>
				<option value="Virgin Islands, British">Virgin Islands, British</option>
				<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
				<option value="Wallis and Futuna">Wallis and Futuna</option>
				<option value="Yemen">Yemen</option>
				<option value="Zambia">Zambia</option>
				<option value="Zimbabwe">Zimbabwe</option>
				<option value="Åland Islands">Åland Islands</option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
state<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[state_name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['state_name'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
	</tr>
</table>

</fieldset>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Contacts<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="contacts">
<table>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[email]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['email'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
email2<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[email2]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['email2'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
phone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[phone]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['phone'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
phone2<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[phone2]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['phone2'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
	</tr>
	<tr>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
fax<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[fax]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['fax'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
		<th><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
website<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
		<td><input type="text" name="data[website]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars($_smarty_tpl->tpl_vars['object']->value['website'], ENT_QUOTES, 'UTF-8', true));?>
" /></td>
	</tr>
</table>

</fieldset>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="note">
	<textarea name="data[description]" class="mce description" style="font-size:13px; width:510px; height:150px;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['description'])===null||$tmp==='' ? '' : $tmp);?>
</textarea>
</fieldset><?php }} ?>