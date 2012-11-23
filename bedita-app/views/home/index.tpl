<script type="text/javascript">
<!--
var urlToSearch = "{$html->url('/home/search')}" 

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

{*$view->element('modulesmenu')*}

{include file="inc/menuleft.tpl"}

<!-- /////////////////////////////// -->

<div class="dashboard">

	<div class="publishingtree">
	{assign_associative var="options" treeParams=['controller' => 'areas']}
	{$view->element('tree',$options)}
	</div>

	<div class="tab"><h2>{t}your 5 recent items{/t}</h2></div>
	<ul id="recent" class="bordered smallist">
	{foreach from=$lastModBYUser item=item}
		<li><span class="listrecent {$item.ObjectType.module_name}">&nbsp;</span>
		<a class="{$item.BEObject.status|default:''}" title="{$item.ObjectType.module_name} | {t}modified on{/t} {$item.BEObject.modified|date_format:$conf->dateTimePattern}" href="{$html->url('/')}{$item.ObjectType.module_name}/view/{$item.BEObject.id}">
			{$item.BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{foreachelse}
		<li><i>{t}you have no recent items{/t}</i></li>
	{/foreach}
	</ul>
	
	<div class="tab"><h2>{t}recent items{/t}</h2></div>
	<ul id="allrecent" class="bordered smallist">
	{foreach from=$lastMod item=item}
		<li>
			<span class="listrecent {$item.ObjectType.module_name}">&nbsp;&nbsp;</span>
			&nbsp;<a class="{$item.BEObject.status|default:''}" title="{$item.ObjectType.module_name} | {t}modified on{/t} {$item.BEObject.modified|date_format:$conf->dateTimePattern}" href="{$html->url('/')}{$item.ObjectType.module_name}/view/{$item.BEObject.id}">
				{$item.BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
	</ul>	
	
</div>

<!-- /////////////////////////////// -->

<div class="dashboard center">
	
	{if !empty($moduleList)}
	<ul class="modules">
	{foreach from=$moduleList key=k item=mod}
	{if ($mod.status == 'on')}
		{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
		<li class="{$mod.name}">
			<a href="{$link}" title="{t}{$mod.label}{/t}">{t}{$mod.label}{/t}</a>
		</li>
	{/if}
	{/foreach}
	</ul>
	{/if}
</div>

<!-- /////////////////////////////// -->

<div class="dashboard right">

	<div class="tab"><h2>{t}search{/t}</h2></div>
	<div id="search">
		<form action="">
			<input type="text" style="width:190px; margin-bottom:5px; padding:5px; " name="searchstring" id="searchstring" value=""/>
			<input id="searchButton" type="button" value="{t}go{/t}" />
		</form>
		<div id="searchResult"></div>	
	</div>
	
	{*bedev}
	<div class="tab"><h2>{t}quick item{/t}</h2></div>
	<div id="new" class="bordered smallist">
		<form>
			<label>{t}Title{/t}</label>
			<input type="text">
			
			<label>{t}Text{/t}</label>
			<textarea></textarea>
			
			<label >{t}Object type{/t}</label>		
			<select>
			{assign var=leafs value=$conf->objectTypes.leafs}
			{foreach from=$conf->objectTypes item=type key=key}	
				{if ( in_array($type.id,$leafs.id) && is_numeric($key) )}
				<option {if ($type.name == 'document')}selected="selected"{/if}>	
					{t}{$type.model}{/t}
				</option>
				{/if}
			{/foreach}
			</select>
			<label>{t}Position{/t}</label>
			<select>
				{$beTree->option($tree)}
			</select>
			<hr />
			<input type="submit" value="{t}publish{/t}"/> <input type="submit" value="{t}save draft{/t}"/>
		</form>
	</div>
	{/bedev*}

<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{$html->url('/tags/listAllTags/1')}", function() {
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


	{if isset($moduleList.tags)}	
	<div class="tab"><h2 id="callTags">{t}tags{/t}</h2></div>
	<div id="tags">
		<div id="loadingTags" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>	
		<div id="listExistingTags" class="tag graced" style="display: none; text-align:justify;"></div>
	</div>
	{/if}
	
	<div class="tab"><h2>{t}last notes{/t}</h2></div>
	<ul id="lastnotes" class="bordered">
		{foreach from=$lastNotes item="note"}
			<li>{$note.realname|default:$note.userid}, 
			{t}on{/t} "<i><a href="{$html->url('/')}view/{$note.ReferenceObject.id}">{$note.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no notes{/t}</li>
		{/foreach}
	</ul>

	{if isset($moduleList.comments)}	
	<div class="tab"><h2>{t}last comments{/t}</h2></div>
	<ul id="lastcomments" class="bordered">
		{foreach from=$lastComments item="cmt"}
			<li>{$cmt.author|default:''}, 
			{t}on{/t} "<i><a href="{$html->url('/')}view/{$cmt.id}">{$cmt.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no comments{/t}</li>
		{/foreach}
	</ul>
	{/if}

	<div class="tab"><h2>{t}connected user{/t}</h2></div>
	<ul id="connected" class="bordered">
	{section name="i" loop=$connectedUser}
		{foreach from=$connectedUser[i] key=usr item=usrdata}
		<li>
		{if isset($moduleList.admin)}
		<a title="{$usrdata.realname} | {$usrdata.userAgent} | {$usrdata.ipNumber}" href="{$html->url('/')}admin/viewUser/{$usrdata.id}">{$usr}</a>
		{else}		
		<a title="{$usrdata.realname}" href="#">{$usr}</a>
		{/if}
		</li>
		{/foreach}
	{/section}
	</ul>
		
</div>
