<script type="text/javascript">
<!--
var urlToSearch = "{$html->url('/home/search')}" 
{literal}
function loadSearch() {
	$("#searchResult").load(urlToSearch, {searchstring: $("input[@name='searchstring']").val()}, function() {
			
	});
}

$(document).ready(function() {
	
	$("#searchButton").click(function() {
		loadSearch();
	});
	
	$("input[@name='searchstring']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			loadSearch();
		}
	});
	
});

    $(document).ready(function(){	
		openAtStart("#search");
    });
	
//-->
</script>
{/literal}

</head>

<body class="home">

<ul class="modules">

    <li class="bedita" rel="{$html->url('/')}">
		{$conf->projectName|default:$conf->userVersion}
	</li>
	
{foreach name=module1 from=$moduleList key=k item=mod}
	{if ($mod.status == 'on')}
	
            {assign_concat var='linkPath' 0=$html->url('/') 1=$mod.path}

            <li {if ($mod.flag & BEDITA_PERMS_READ) }
					class="{$mod.name}" rel="{$linkPath}"
				{else}
					class="{$mod.name} off"
				{/if}>
            	{t}{$mod.label}{/t}
				{*<br /><span style="font-size:1.5em" class="graced">{$mod.id}</span>*}
			</li>	
	{/if}
		
    {if $smarty.foreach.module1.iteration == 2}
	
	<li class="welcome">
		<a href="javascript:void(0)" onClick="$('#userpreferences').BEtabstoggle();">
		<h1>{t}welcome{/t}</h1>
		{$BEAuthUser.realname}</a>
		
	</li>
	
	{/if}
	
{/foreach}

	<li class="colophon">

		{if !empty($conf->multilang) && $conf->multilang}
			{foreach key=key item=item name=l from=$conf->langsSystem}
				<a {if $session->read('Config.language') == $key}class="on"{/if} href="{$html->base}/lang/{$key}">› {$item}</a>
				<br />
			{/foreach} 
		{/if}	
		<hr />
		{include file="../common_inc/colophon.tpl"}
		<hr />
		<a href="{$html->url('/authentications/logout')}">› {t}Exit{/t}</a>
	</li>
	

	
</ul> 

<div style="position:absolute; left:580px; top:19px; z-index:400">{include file="../common_inc/messages.tpl"}</div>

<div class="dashboard">



<h1>{*t}dashboard{/t*}</h1>


<div class="tab"><h2>{t}your 5 recent items{/t}</h2></div>
	<ul id="recent" class="bordered">
	{section name="n" loop=$lastModBYUser}
		<li><span class="listrecent {$lastModBYUser[n].ObjectType.module}">&nbsp;</span>
		{if $lastModBYUser[n].BEObject.object_type_id == $conf->objectTypes.mailtemplate.id}
		{assign var='action' value=viewtemplate}
		{else}
		{assign var='action' value=view}
		{/if}
		<a title="{$lastModBYUser[n].ObjectType.module} | {$lastModBYUser[n].BEObject.modified}" href="{$html->url('/')}{$lastModBYUser[n].ObjectType.module}/{$action}/{$lastModBYUser[n].BEObject.id}">
			{$lastModBYUser[n].BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{sectionelse}
		<li><i>{t}you have no recent items{/t}</i></li>
	{/section}
	</ul>
	
<div class="tab"><h2>{t}your profile and preferences{/t}</h2></div>
<div id="userpreferences">
	
	{include file="inc/userpreferences.tpl"}

</div>


<div class="tab"><h2>{t}search{/t}</h2></div>
	<div id="search">
		<form action="">
			{*<label class="block" for="searchstring">{t}search string{/t}:</label>*}
			<input type="text" name="searchstring" id="searchstring" value=""/>
			&nbsp;<input id="searchButton" type="button" value="{t}go{/t}" />
			<hr />
		</form>
	<div id="searchResult"></div>
	
	</div>


<div class="tab"><h2>{t}all recent items{/t}</h2></div>
	<ul id="allrecent" class="bordered">
	{section name="n" loop=$lastMod}
		{if $lastMod[n].BEObject.object_type_id == $conf->objectTypes.mailtemplate.id}
		{assign var='action' value=viewtemplate}
		{else}
		{assign var='action' value=view}
		{/if}
		<li>
			<span class="listrecent {$lastMod[n].ObjectType.module}">&nbsp;&nbsp;</span>
			&nbsp;<a title="{$lastMod[n].ObjectType.module} | {$lastMod[n].BEObject.modified}" href="{$html->url('/')}{$lastMod[n].ObjectType.module}/{$action}/{$lastMod[n].BEObject.id}">
				{$lastMod[n].BEObject.title|strip_tags|truncate:36:"~":true|default:'<i>[no title]</i>'}</a></li>
	{/section}
	</ul>

{bedev}
<div class="tab"><h2>{t}last editor's notes{/t}</h2></div>
	<ul id="lastnotes" class="bordered">
		{foreach from=$lastNotes item="note"}
			<li>{$note.realname|default:$note.userid}, 
			{t}on{/t} "<i><a href="{$html->url('/')}view/{$note.ReferenceObject.id}">{$note.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no notes{/t}</li>
		{/foreach}
	</ul>

<div class="tab"><h2>{t}last comments{/t}</h2></div>
	<ul id="lastcomments" class="bordered">
		{foreach from=$lastComments item="cmt"}
			<li>{$cmt.author|default:''}, 
			{t}on{/t} "<i><a href="{$html->url('/')}view/{$cmt.ReferenceObject.id}">{$cmt.ReferenceObject.title|strip_tags|truncate:36:'~':true|default:'[no title]'}'</a></i>"</li>
		{foreachelse}
			<li>{t}no comments{/t}</li>
		{/foreach}
	</ul>
{/bedev}
	
<div class="tab"><h2>{t}connected user{/t}</h2></div>
	<ul id="connected" class="bordered">
	{section name="i" loop=$connectedUser}
		{foreach from=$connectedUser[i] key=usr item=usrdata}
		<li>
		{if isset($moduleList.admin) }
		<a title="{$usrdata.realname} | {$usrdata.userAgent} | {$usrdata.ipNumber}" href="{$html->url('/')}admin/viewUser/{$usrdata.id}">{$usr}</a>
		{else}		
		<a title="{$usrdata.realname}" href="#">{$usr}</a>
		{/if}
		</li>
		{/foreach}
	{/section}
	</ul>

{*include file="./inc/messageboard.tpl"*}


{literal}
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{/literal}{$html->url('/tags/listAllTags/1')}{literal}", function() {
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
{/literal}

<div class="tab"><h2 id="callTags">{t}tags{/t}</h2></div>
<div id="tags">
	<div id="loadingTags" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>	
	<div id="listExistingTags" class="tag graced" style="display: none; text-align:justify;"></div>
</div>





</div>


		
		
		
<p style="clear:both; margin-bottom:20px;" />

