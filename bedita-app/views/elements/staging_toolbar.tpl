<div class="stagingmenu">

	<ul>
		<li style="list-style:none">BE</li>
		<li class="in">STAGING non-public site</li>
		<li class="in">{$publication.staging_url|default:'staging url UNDEFINED'}</li>
		{if !empty($section.currentContent.id)}
		<li class="in">
			<a rel="pageinfo">Page info</a>
		</li>
		<li class="in">
			<a rel="pageedit">Edit page</a>
		</li>
		{/if}
		{if !empty($section.id)}
		<li class="in">
			<a rel="sectionedit">Edit section</a>
		</li>
		{/if}
		<li class="in"><a href="{$html->url('/')}logout">logout</a></li>
		<li class="openclose" style="list-style:none; padding:0px; margin:0px; margin-top:-2px; font-size:2em; cursor:pointer;">
			<a>‹</a>
		</li>
	</ul>

</div>

{if !empty($section.currentContent.id)}
{assign var="current" value=$section.currentContent}
<div id="pageinfo" class="stagingsubmenu">
	<table>
		<tr>
			<th>creator:</th><td>{$current.UserCreated.realname}</td>
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
		{if $current.start}
		<tr>
			<th>scheduled on:</th><td>{$current.start|date_format:"%d %b %Y"}</td>
		</tr>
		{/if}
		{if $current.end}
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
			<td><a target="_blank" href="{$conf->beditaUrl}/view/{$current.id}">{$current.title|truncate:64|default:"[no title]"}</a></td>
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
				<td><a target="_blank" href="{$conf->beditaUrl}/view/{$r.id}">{$r.title|truncate:64|default:"[no title]"}</a></td>
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
			<td><a target="_blank" href="{$conf->beditaUrl}/view/{$section.id}">{$section.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{if !empty($section.childContents)}
		<tr>
			<th>SECTION CONTENTS</th>
			<td></td>
		</tr>
		{foreach from=$section.childContents item=con}
		<tr>
			<th>child content:</th>
			<td><a target="_blank" href="{$conf->beditaUrl}/view/{$con.id}">{$con.title|truncate:64|default:"[no title]"}</a></td>
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
			<td><a target="_blank" href="{$conf->beditaUrl}/view/{$sec.id}">{$sec.title|truncate:64|default:"[no title]"}</a></td>
		</tr>
		{/foreach}
		{/if}
	</table>
</div>
{/if}


