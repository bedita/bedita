<script type="text/javascript">
<!--
var urlToSearch = "{$this->Html->url('/home/search')}" 

function loadSearch() {
	$("#searchResult").load(urlToSearch, { searchstring: $("input[name='searchstring']").val() }, function() {
		//			
	});
}

$(document).ready(function() {
	
	$("#searchButton").click(function() {
		loadSearch();
	});
	
	$("input[name='searchstring']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			loadSearch();
		}
	});
	
});

    $(document).ready(function(){	
		openAtStart("#search, #allrecent, #lastnotes, #lastcomments, #recent, #userpreferences");
    });
	
//-->
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

	<div class="welcome" style="position:absolute; top:20px; left:180px">
		
		<h1><span a style="font-size:0.6em">{t}welcome{/t}</span>
			<a style="font-size:0.8em" href="javascript:void(0)" onClick="$('#userpreferences').prev('.tab').click();">{$BEAuthUser.realname}</a>
		</h1>
	</div>
	
<div class="dashboard">
	
	<span class="hometree">
	{assign_associative var="options" home=true}
	{$view->element('tree',$options)}
	</span>

	<div class="tab"><h2>{t}search{/t}</h2></div>
	<div id="search">
		<form action="">
			{*<label class="block" for="searchstring">{t}search string{/t}:</label>*}
			<input type="text" style="width:210px" name="searchstring" id="searchstring" value=""/>
			&nbsp;<input id="searchButton" type="button" value="{t}go{/t}" />
			<hr />
		</form>
		<div id="searchResult"></div>	
	</div>


	<div class="tab"><h2>{t}all recent items{/t}</h2></div>
	<ul id="allrecent" class="bordered smallist">
	{foreach from=$lastMod item=item}
		<li>
			<span class="listrecent {$item.ObjectType.module_name}">&nbsp;&nbsp;</span>
			&nbsp;<a class="{$item.BEObject.status|default:''}" title="{$item.ObjectType.module_name} | {t}modified on{/t} {$item.BEObject.modified|date_format:$conf->dateTimePattern}" href="{$this->Html->url('/')}{$item.ObjectType.module_name}/view/{$item.BEObject.id}">
				{$item.BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
	</ul>
	
</div>

<div class="dashboard first">

	{if !empty($this->Html->params.named.id)}
	<div class="tab"><h2>{t}items{/t} in nomesezione ({$this->Html->params.named.id})</h2></div>
	<ul id="allrecent" class="bordered smallist">
	{foreach from=$lastMod item=item}
		<li>
			<span class="listrecent {$item.ObjectType.module_name}">&nbsp;&nbsp;</span>
			&nbsp;<a class="{$item.BEObject.status|default:''}" title="{$item.ObjectType.module_name} | {t}modified on{/t} {$item.BEObject.modified|date_format:$conf->dateTimePattern}" href="{$this->Html->url('/')}{$item.ObjectType.module_name}/view/{$item.BEObject.id}">
				{$item.BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
	</ul>
	{/if}
	
	{bedev}
	<div class="tab"><h2>{t}quick item{/t}</h2></div>
	<div id="new" class="bordered smallist">
		<form>
			<label>{t}Title{/t}</label>
			<input type="text" style="width:250px">
			<label>{t}Text{/t}</label>
			<textarea style="width:250px"></textarea>
			<label >{t}Object type{/t}</label>		
			<select style="width:250px">
			{assign var=leafs value=$conf->objectTypes.leafs}
			{foreach from=$conf->objectTypes item=type key=key}	
				{if ( in_array($type.id,$leafs.id) && is_numeric($key) )}
				<option {if ($type.name == 'document')}selected="selected"{/if}>	
					{t}{$type.model}{/t}
				</option>
				{/if}
			{/foreach}
			</select>
			
			<br />
			<label>{t}Position{/t}</label>
			<select style="width:250px">
				{$this->BeTree->option($tree)}
			</select>
			<hr />
			<input type="submit" value="{t}publish{/t}"/> <input type="submit" value="{t}save draft{/t}"/>
		</form>
	</div>
	{/bedev}

	<div class="tab"><h2>{t}last notes{/t}</h2></div>
	<ul id="lastnotes" class="bordered">
		{foreach from=$lastNotes item="note"}
			<li>{$note.realname|default:$note.userid}, 
			{t}on{/t} "<i><a href="{$this->Html->url('/')}view/{$note.ReferenceObject.id}">{$note.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no notes{/t}</li>
		{/foreach}
	</ul>

	<div class="tab"><h2>{t}last comments{/t}</h2></div>
	<ul id="lastcomments" class="bordered">
		{foreach from=$lastComments item="cmt"}
			<li>{$cmt.author|default:''}, 
			{t}on{/t} "<i><a href="{$this->Html->url('/')}view/{$cmt.id}">{$cmt.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no comments{/t}</li>
		{/foreach}
	</ul>


<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{$this->Html->url('/tags/listAllTags/1')}", function() {
				$("#loadingTags").slideUp("fast");
				$("#listExistingTags").slideDown("fast");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
			} else {
				$("#listExistingTags").slideDown("fast");
			}
			showTags = !showTags;
		}
	});	
});
//-->
</script>

	
	<div class="tab"><h2 id="callTags">{t}tags{/t}</h2></div>
	<div id="tags">
		<div id="loadingTags" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>	
		<div id="listExistingTags" class="tag graced" style="display: none; text-align:justify;"></div>
	</div>
	
</div>

<div class="dashboard second">

	<div class="tab"><h2>{t}your 5 recent items{/t}</h2></div>
	<ul id="recent" class="bordered smallist">
	{foreach from=$lastModBYUser item=item}
		<li><span class="listrecent {$item.ObjectType.module_name}">&nbsp;</span>
		<a class="{$item.BEObject.status|default:''}" title="{$item.ObjectType.module_name} | {t}modified on{/t} {$item.BEObject.modified|date_format:$conf->dateTimePattern}" href="{$this->Html->url('/')}{$item.ObjectType.module_name}/view/{$item.BEObject.id}">
			{$item.BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{foreachelse}
		<li><i>{t}you have no recent items{/t}</i></li>
	{/foreach}
	</ul>
			
	<div class="tab"><h2>{t}your profile and preferences{/t}</h2></div>
	
	<div id="userpreferences">	
		{include file="inc/userpreferences.tpl"}
	</div>

	<div class="tab"><h2>{t}connected user{/t}</h2></div>
	<ul id="connected" class="bordered">
	{section name="i" loop=$connectedUser}
		{foreach from=$connectedUser[i] key=usr item=usrdata}
		<li>
		{if isset($moduleList.admin)}
		<a title="{$usrdata.realname} | {$usrdata.userAgent} | {$usrdata.ipNumber}" href="{$this->Html->url('/')}admin/viewUser/{$usrdata.id}">{$usr}</a>
		{else}		
		<a title="{$usrdata.realname}" href="#">{$usr}</a>
		{/if}
		</li>
		{/foreach}
	{/section}
	</ul>
	
</div>	
		
		
<p style="clear:both; margin-bottom:20px;" />

