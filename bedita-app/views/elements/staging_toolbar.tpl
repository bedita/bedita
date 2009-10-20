{literal}
<style type="text/css">

BODY {
	/*
padding-top:30px;
*/
}

.BEdita_staging_toolbar {
	font-size:13px !important;
	font-family: 'Lucida grande', 'Segoe UI', Verdana, Arial, Helvetica, sans-serif !important; 
	/*
	font: normal 11px "Arial",sans-serif !important
	*/
}

.stagingmenu {	
	top:0px;
	padding:5px 5px 5px 5px !important; 
	margin:5px 5px 5px 5px !important; 
	text-align:center;
	border-radius : 7px; 
	-moz-border-radius : 7px; /* Mozilla */
	-webkit-border-radius : 7px; /* Webkit */
}

.stagingmenu, .stagingsubmenu {
	background-color: #333;
	position:absolute;
	z-index:500;
	/*
opacity:0.8;
*/
}

.stagingsubmenu table TR:hover {
	background-color:#666;
}
.BEdita_staging_toolbar UL {
	margin:0px 0px 0px 0px !important;
	padding:0px 0px 0px 0px !important;
}


.stagingmenu LI {
	color: silver;
	float:left;
	margin-top:0px;
	margin-left:20px;
	padding:0px 20px 0px 0px;
	border-right:0px solid silver;
	white-space:nowrap;
}

.stagingmenu LI A {
	color: silver;
	text-decoration:none;
	cursor:pointer;
}

.stagingmenu LI A:Hover {
	color: #FFF;
}

.stagingsubmenu {
	/*
width:250px;
*/

	/*
border-radius : 7px; 
	-moz-border-radius : 7px; 
	-webkit-border-radius : 7px; 
*/
	display:none;
	top:38px;
}

.stagingsubmenu table {
	border-collapse:collapse;
	margin:10px;
}

.stagingsubmenu table * {
	text-align:left;
	font-size: 11px;
	color:white;
	padding:4px;
	vertical-align:top;
}

.stagingsubmenu table TH {
	text-align:right;
	border-right:1px solid gray;
	white-space:nowrap;
}


	

.stagingmenu ::selection {
	background: none; /* Safari */
	}
.stagingmenu ::-moz-selection {
	background: none; /* Firefox */
}

</style>

<script type="text/javascript">
$(document).ready(function(){

/*
	$(".stagingmenu LI A").click(function(){
		var myLeft = $(this).position().left;
		var trigged  = $(this).attr("rel");
		$(".stagingsubmenu").slideUp('normal');
		$("#"+trigged+"").css("left",myLeft).slideDown('normal');
	});
*/

$(".stagingmenu LI A").toggle(
      function () {
       	var myLeft = $(this).position().left;
		var trigged  = $(this).attr("rel");
		$(".stagingsubmenu").slideUp('normal');
		$("#"+trigged+"").css("left",myLeft).slideDown('normal');
      },
      function () {
		$(".stagingsubmenu").slideUp('normal');
      }
    );




	$(".openclose").click(function(){
		$(".stagingsubmenu").hide();
		$(".stagingmenu LI.in").toggle('normal');
		$(".openclose.arrow").toggleText("›","‹");
	});


});
</script>
	
{/literal}

<div class="BEdita_staging_toolbar">
<div class="stagingmenu">
	<ul>
		<li class="openclose" style="font-size:16px; font-family:'Georgia', sans-serif; cursor:pointer; list-style:none">BE</li>
		<li class="in">STAGING non-public site</li>
{*		<li class="in">{$publication.staging_url}</li>*}
		{if !empty($section.currentContent.id)}
		<li class="in">
			<a rel="pageinfo">Page info</a>
		</li>
		<li class="in">
			<a rel="pageedit">Edit this page</a>
		</li>
		{/if}
		{if !empty($section.id)}
		<li class="in">
			<a rel="sectionedit">Edit this section</a>
		</li>
		{/if}
		
		{if !empty($BEAuthUser)}
		<li class="in"><a href="{$html->url('/')}logout">logout</a></li>
		{else}
		<li class="in">To edit contents please authenticate</li>
		{/if}
		
		<li class="openclose arrow" style="list-style:none; padding:0px; margin:0px; margin-top:-2px; font-size:2em; cursor:pointer;">
			<a>‹</a>
		</li>
	</ul>

</div>


{if !empty($section.currentContent.id)}
{assign var="current" value=$section.currentContent}
<div id="pageinfo" class="stagingsubmenu">
	<table>
		<tr>
			<th>creator:</th><td>{$current.UserCreated.realname|default:$current.UserCreated.userid}</td>
		</tr>
		<tr>
			<th>modified on:</th><td>{$current.modified|date_format:"%d %b %Y %H:%M:%S"}</td>
		</tr>
		<tr>
			<th>type:</th><td>{$current.object_type}</td>
		</tr>
		<tr>
			<th>status:</th><td>{$current.status|upper}</td>
		</tr>
		{if !empty($current.start)}
		<tr>
			<th>scheduled on:</th><td>{$current.start|date_format:"%d %b %Y"}</td>
		</tr>
		{/if}
		{if !empty($current.end)}
		<tr>
			<th>until on:</th><td>{$current.end|date_format:"%d %b %Y"}</td>
		</tr>
		{/if}
		<tr>
			<th>lang:</th><td>{$current.lang}</td>
		</tr>
		<tr>
			<th>id:</th><td>{$current.id}</td>
		</tr>
		<tr>
			<th>nickname:</th><td>{$current.nickname}</td>
		</tr>
	</table>
</div>
<div id="pageedit" class="stagingsubmenu">
	<table>
		<tr>
			<th>current content [{$current.lang}]:</th>
			<td><a title="nickname: {$current.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$current.id}">{$current.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{if !empty($current.curr_lang)}
		<tr>
			<th>[{$current.curr_lang}] translation  [current]:</th>
			<td><a target="_blank" href="{$conf->beditaUrl}/translations/view/{$current.id}/{$current.curr_lang}">{$current.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/if}
		{foreach from=$current.languages item=tit key=lang}
		{if $lang != $current.lang}
		<tr>
			<th>[{$lang}] translation:</th>
			<td><a target="_blank" href="{$conf->beditaUrl}/translations/view/{$current.id}/{$lang}">{$tit.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/if}
		{/foreach}
		{if !empty($current.relations)}
		<tr>
			<th>RELATIONS</th>
			<td></td>
		</tr>
		
		{foreach from=$current.relations item=related key=relname}
		{foreach from=$related item=r}
		<tr>
			<th>{$relname}:</th>
			<td><a title="nickname: {$r.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$r.id}">{$r.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/foreach}
		
		{/if}

	</table>
</div>
{/if}
{if !empty($section.id)}
<div id="sectionedit" class="stagingsubmenu">
	<table>
		<tr>
			<th>current section:</th>
			<td><a title="nickname: {$section.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$section.id}">{$section.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{if !empty($section.childContents)}
		<tr>
			<th>SECTION CONTENTS</th>
			<td></td>
		</tr>
		{foreach from=$section.childContents item=con}
		<tr>
			<th>child content:</th>
			<td><a title="nickname: {$con.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$con.id}">{$con.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/if}
		{if !empty($section.childSections)}
		<tr>
			<th>SUBSECTIONS</th>
			<td></td>
		</tr>
		{foreach from=$section.childSections item=sec}
		<tr>
			<th>child section:</th>
			<td><a title="nickname: {$sec.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$sec.id}">{$sec.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/if}
	</table>
</div>
{/if}
</div>

