<div data-role="page">

	<div data-role="header">
		<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
		<a href="#" data-rel="back" data-icon="arrow-l" data-iconpos="notext">{t}Back{/t}</a>
	</div><!-- /header -->

	<div data-role="content">

  {strip}
	<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
		<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

		<ul data-role="listview">
			{* Title *}
			<li data-role="list-divider">{t}Title{/t}</li>
			
			<li data-role="fieldcontain">
				<label for="data[title]">{t}Title{/t}:</label>
				<input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject" style="width:100%" />
			</li>
			
			<li data-role="fieldcontain">
				<label for="data[description]">{t}Description{/t}:</label>
				<textarea style="width:100%;" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
			</li>
			
			<li data-role="fieldcontain">
				<label for="data[nickname]">{t}Unique name{/t} ({t}url name{/t}):</label>
				<input type="text" name="data[nickname]" style="font-style:italic; width:100%" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>
			</li>

			{* Text *}
			<li data-role="list-divider">{t}Text{/t}</li>
			{if (!empty($addshorttext)) or (!empty($object.abstract))}
			<li data-role="fieldcontain">
				<label for="data[abstract]">{t}short text{/t}:</label>
				<textarea style="width:100%;" name="data[abstract]">{$object.abstract|default:''}</textarea>
			</li>
			{/if}
			<li data-role="fieldcontain">
				<label for="data[body]">{t}long text{/t}:</label>
				<textarea style="width:100%;" name="data[body]">{$object.body|default:''}</textarea>
			</li>
			
			{* Properties *}
			<li data-role="list-divider">{t}Properties{/t}</li>
			<li data-role="fieldcontain">
				<label for="data[status]">{t}status{/t}:</label>
				{if $object.fixed}
					{*t}This object is fixed - some data is readonly{/t*}
					<input type="hidden" name="data[status]" value="{$object.status}" />
				{else}
					{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
				{/if}
			</li>
			{if in_array('administrator',$BEAuthUser.groups)}
			<li data-role="fieldcontain">
				<label for="data[fixed]">{t}fixed{/t}:</label>
				<select name="data[fixed]" data-role="slider">
					<option value="off" {if empty($object.fixed)}selected{/if}>0</option>
					<option value="on" {if !empty($object.fixed)}selected{/if}>1</option>
				</select>
			</li>
			{else}
				<input type="hidden" name="data[fixed]" value="{$object.fixed}" />
			{/if}
			{*
		<th>{t}status{/t}:</th>
		<td colspan="4">
			{if $object.fixed}
				{t}This object is fixed - some data is readonly{/t}
				<input type="hidden" name="data[status]" value="{$object.status}" />
			{else}
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
			{/if}
			
			{if in_array('administrator',$BEAuthUser.groups)}
				&nbsp;&nbsp;&nbsp; <b>fixed</b>:&nbsp;&nbsp;<input type="checkbox" name="data[fixed]" value="1" {if !empty($object.fixed)}checked{/if} />
			{else}
				<input type="hidden" name="data[fixed]" value="{$object.fixed}" />
			{/if}
		</td>
	</tr>

			

	{if !(isset($publication)) || $publication}

	<tr>
		<td colspan="2">
			<label>{t}scheduled from{/t}:</label>&nbsp;
			
			
			<input size="10" type="text" style="vertical-align:middle"
			class="dateinput" name="data[start_date]" id="start"
			value="{if !empty($object.start_date)}{$object.start_date|date_format:$conf->datePattern}{/if}" />
			&nbsp;
			
			<label>{t}to{/t}: </label>&nbsp;
			
			<input size="10" type="text" 
			class="dateinput" name="data[end_date]" id="end"
			value="{if !empty($object.end_date)}{$object.end_date|date_format:$conf->datePattern}{/if}" />

		</td>
	</tr>

	{/if}

	<tr>
		<th>{t}author{/t}:</th>
		<td>
			<input type="text" name="data[creator]" value="{$object.creator}" />
			<input type="hidden" name="data[user_created]" value="{$object.user_created}" />
		</td>
	</tr>


	<tr>
		<th>{t}main language{/t}:</th>
		<td>
		{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
		<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			{foreach key=val item=label from=$conf->langsIso name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
		</select>
		</td>
	</tr>
	
	{if isset($comments)}
	<tr>
		<th>{t}comments{/t}:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"{if empty($object.comments) || $object.comments=='off'} checked{/if}/>{t}No{/t} 
			<input type="radio" name="data[comments]" value="on"{if !empty($object.comments) && $object.comments=='on'} checked{/if}/>{t}Yes{/t}
			<input type="radio" name="data[comments]" value="moderated"{if !empty($object.comments) && $object.comments=='moderated'} checked{/if}/>{t}Moderated{/t}
			&nbsp;&nbsp;
			{if isset($moduleList.comments) && $moduleList.comments.status == "on"}
				{if !empty($object.num_of_comment)}
					<a href="{$html->url('/')}comments/index/comment_object_id:{$object.id}"><img style="vertical-align:middle" src="{$html->webroot}img/iconComments.gif" alt="comments" /> ({$object.num_of_comment}) {t}view{/t}</a>
				{/if}
			{/if}
		</td>
	</tr>
	{/if}
	
	<tr>
		<th>{t}duration in minutes{/t}:</th>
		<td>
			<input type="text" name="data[duration]" value="{if !empty($object.duration)}{$object.duration/60}{/if}" />
		</td>
	</tr>
</table>
	
</fieldset>
*}
		</ul>
	</form>	

  {/strip}

	</div><!-- /content -->
	{$view->element('footer')}
</div>