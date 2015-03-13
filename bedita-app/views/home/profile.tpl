{$view->element('modulesmenu')}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#bioprofile, #alerts, #mypublications";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
<style>
	input[type=text],input[type=password] {
		padding:5px 0px 5px 4px;
	}
</style>
{/literal}

{include file = './inc/menuleft.tpl'}

<div class="head">

	<div class="toolbar" style="padding-top:20px">
	
		<h1 style="display:inline; padding-right:20px;">{$BEAuthUser.realname|escape}</h1><br>
		<span style="white-space: nowrap">
		{t}Created on{/t} <a>{$BEAuthUser.created|date_format:$conf->dateTimePattern}</a>
		/
		{t}Last access on:{/t} <a>{$BEAuthUser.last_login|date_format:$conf->dateTimePattern}</a>
		</span>
	 </div>
	 
</div>

{include file = './inc/menucommands.tpl' method = 'profile' fixed = true}

<div class="mainfull">
	
	<form action="{$html->url('/home/editProfile')}" id="editProfile" method="post">
		{$beForm->csrf()}
		
		<div style="width:260px; float:left">
		
			<div class="tab"><h2>{t}Personal data{/t}</h2></div>

			{include file = './inc/userpreferences.tpl'}
		
		</div>

		<div style="margin-left:10px; width:260px; float:left">
			
			<div class="tab"><h2>{t}Email notifications{/t}</h2></div>

			<fieldset id="alerts">
			
			<script type="text/javascript">
			$(document).ready(function(){
			$(".checko").change(function(){
				var target = $(this).attr('rel');
				if ($(this).is(':checked'))	{
				  	$('#'+target).show().val(['all']);
				} else {
					$('#'+target).hide().val(['never']);
				}
			});
			});
			</script>
			
			<table class="condensed" style="width: 100%">
			<!--
			<tr>
				<td colspan=2><label>{t}notify me by email{/t}</label></td>
			</tr>
			-->
			<tr>
				<td>
					<input class="checko" name="comments" value="1" rel="usercomments" type="checkbox" {if !empty($BEAuthUser.comments) && ($BEAuthUser.comments != "never")} checked{/if}>
					{t}new comments{/t}
				</td>
				<td>
					<select id="usercomments" name="data[User][comments]" {if empty($BEAuthUser.comments) or ($BEAuthUser.comments == "never")}style="display:none"{/if}>
						<option value="mine"{if $BEAuthUser.comments == "mine"} selected{/if}>{t}on my stuff only{/t}</option>
						<option value="all"{if $BEAuthUser.comments == "all"} selected{/if}>{t}all{/t}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<input class="checko" name="notes" value="1" rel="usernotes" type="checkbox" {if !empty($BEAuthUser.notes) && ($BEAuthUser.notes != "never")} checked{/if}>
					{t}new notes{/t}</td>
				<td>
					<select id="usernotes" name="data[User][notes]" {if empty($BEAuthUser.notes) or ($BEAuthUser.notes == "never")}style="display:none"{/if}> 
						<option value="mine"{if $BEAuthUser.notes == "mine"} selected{/if}>{t}on my stuff only{/t}</option>
						<option value="all"{if $BEAuthUser.notes == "all"} selected{/if}>{t}all{/t}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<input type="checkbox" name="data[User][notify_changes]" value="1"{if $BEAuthUser.notify_changes == 1} checked{/if}>
					{t}changes on my contents{/t}
				</td>
			</tr>
			</table>
			</fieldset>

		</div>
		
		<div style="margin-left:10px; width:260px; float:left">
			<div class="tab"><h2>{t}My permissions groups{/t}</h2></div>
			<fieldset id="mypublications">
				<ul style="list-style:disc; margin-left:20px;">
				{foreach from=$BEAuthUser.groups item=item}
					<li>{$item}</li>
				{/foreach}
				</ul>
			</fieldset>
		</div>

	</form>

<!--
		<hr />
		<input type="submit" value="{t}save profile{/t}" />
		<br style="clear:both" />
	{dump var=$BEAuthUser}-->
	
</div>