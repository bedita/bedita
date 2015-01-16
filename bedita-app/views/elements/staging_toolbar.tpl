
<div id="BEdita_staging_toolbar">
<div class="stagingmenu">
	<ul>
		<li class="openclose" style="font-size:1.2em; font-family:'Georgia', sans-serif; cursor:pointer; list-style:none">BE</li>
		<li class="in">STAGING non-public site</li>
{*		<li class="in">{$publication.staging_url}</li>*}
		{if !empty($section.currentContent.id)}
		<li class="in">
			<a rel="pageinfo">Edit this page</a>
		</li>
		
		<!-- CONTENT EDITABLE !!!! 
		<li class="in">
			<input type="button" class="inlinemodify" value=" Activate inline modify " />
		</li>
		<li class="in" style="display:none">
			<input type="button" class="pagesubmit" value=" Save this page " />
		</li>
		<li class="in" style="display:none">
			<input type="button" class="pagecancel" value=" cancel " />
		</li>
		-->
		
		{/if}
		
		{if !empty($section.id)}
		<li class="in">
			<a rel="sectionedit">Edit this section</a>
		</li>
		{/if}
		
		<li class="in">
			<a rel="grid" class="grid" href="#">show grid</a>
		</li>
		
		
		{if !empty($BEAuthUser)}
		<li class="in"><a href="{$html->url('/')}logout">logout</a></li>
		{else}
		<li class="in">To edit contents please authenticate</li>
		{/if}
		
		<li class="openclose arrow" style="list-style:none; padding:0px; margin:0px; margin-top:-2px; font-size:2em; cursor:pointer;">
			‹
		</li>
	</ul>

</div>


{if !empty($section.currentContent.id)}
{assign var="current" value=$section.currentContent}
<div id="pageinfo" class="stagingsubmenu">
	<table>
		<tr>
			<th>current content [{$current.lang}] :</th>
			<td><a title="nickname: {$current.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$current.id}">{$current.title|escape|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{foreach from=$current.languages item=tit key=lang}
		{if $lang != $current.lang}
		<tr>
			<th>translation [{$lang}] :</th>
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
			<td><a title="nickname: {$r.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$r.id}">{$r.title|escape|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/foreach}
		
		{/if}
<tr  style="border-top:1px solid gray">
			<th>creator:</th><td>{$current.UserCreated.realname|default:$current.UserCreated.userid|escape}</td>
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
		{if !empty($current.start_date)}
		<tr>
			<th>scheduled on:</th><td>{$current.start_date|date_format:"%d %b %Y"}</td>
		</tr>
		{/if}
		{if !empty($current.end_date)}
		<tr>
			<th>until on:</th><td>{$current.end_date|date_format:"%d %b %Y"}</td>
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
		<tr>
			<th>alias:</th>
			<td>
				{if (!empty($current.Alias))}
				{foreach from=$current.Alias item=alias}
					{$alias.nickname_alias|default:''}<br />
				{/foreach}
				{/if}
			</td>
		</tr>
	</table>
</div>
{/if}
{if !empty($section.id)}
<div id="sectionedit" class="stagingsubmenu">
	<table>
		<tr>
			<th>current section:</th>
			<td><a title="nickname: {$section.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$section.id}">{$section.title|escape|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{if !empty($section.childContents)}
		<tr>
			<th>SECTION CONTENTS</th>
			<td></td>
		</tr>
		{foreach from=$section.childContents item=con}
		<tr>
			<th>child content:</th>
			<td><a title="nickname: {$con.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$con.id}">{$con.title|escape|truncate:64|default:"[no title]"}</a></td>
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
			<td><a title="nickname: {$sec.nickname}" target="_blank" href="{$conf->beditaUrl}/view/{$sec.id}">{$sec.title|escape|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/if}
	</table>
</div>
{/if}
</div>


