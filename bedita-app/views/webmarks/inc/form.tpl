{*
** document form template
*}

<script type="text/javascript">
var url="{$html->url('/webmarks/checkUrl')}";
{literal}
	function callback(data) {
		$('#http_code_result').text(data.http_code);
	}

	$(document).ready(function(){
		$('#checkUrl').click(function () {
			var postdata = {url:$('#url').val(),id:$('#link_id').val()};
			$.post(url, postdata, callback, "json");
		});
	});
	
{/literal}
</script>

<form action="{$html->url('/webmarks/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

{* title and description *}

<div class="tab"><h2>{t}Title{/t}</h2></div>

<fieldset id="title">
	<input type="hidden" name="data[id]" id="link_id" value="{$object.id|default:''}"/>
	<label>{t}url{/t}:</label>
	<br />
	<input style="width:430px" type="text" id="url" name="data[url]" value="{$object.url|default:''}" />
	{if !empty($object.url)}
	&nbsp; <a style="font-weight:bold;" href="{$object.url}" target="_blank"> GO </a>
	{/if}
	<br />
	<label>{t}title{/t}:</label>
	<br />
	<input style="width:460px" type="text" name="data[title]" value="{$object.title|default:''}" />
	<br />
	<label>{t}description{/t}:</label>
	<br />
	<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''}</textarea>

</fieldset>

<div class="tab"><h2>{t}Properties{/t}</h2></div>
<fieldset id="linkdetails">

	<table class="bordered">
		<tr>
			<th>{t}Status{/t}:</th>
			<td>
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
			</td>
		</tr>
		<tr>
			<th>{t}Last check{/t}:</th>
			<td>{$object.http_response_date|date_format:$conf->dateTimePattern|default:''}</td>
		</tr>
		<tr>
			<th>{t}Last result{/t}:</th>
			<td>{$object.http_code|default:''}</td>
		</tr>
		<tr>
			<td><input type="button" id="checkUrl" value="{t}Check url{/t}" /></td><td><span id="http_code_result"></span></td>
		</tr>
		<tr>
			<th style="vertical-align:top">{t}referenced objects list{/t}:</th>
			<td>
				{if !empty($relObjects.link)}
					{foreach from=$relObjects.link item="o"}
						<li>
							<span class="listrecent {$o.ObjectType.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
							<a title="{$o.created}"  href="{$html->url('/')}{$o.ObjectType.module}/view/{$o.id}">{$o.title}</a>
						</li>
					{/foreach}
				{else}
					{t}no referenced objects{/t}
				{/if}
			</td>
		</tr>
	</table>
	
</fieldset>

{include file="../common_inc/form_tree.tpl"}
	
{include file="../common_inc/form_categories.tpl"}

{include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}

{include file="../common_inc/form_tags.tpl"}

</form>