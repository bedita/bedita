<h2 class="showHideBlockButton">{t}Connect to multimedia gallery{/t}</h2>
<div class="blockForm" id="frmgallery" style="display:none">
{assign var="gallery_id" value=$object.gallery_id|default:0}
{if empty($galleries)}
{t}No galleries found{/t}
{else}
{t}Gallery associated to this document{/t}:<br/>
<select id="galleryForDocument" name="data[gallery_id]" style="width:500px;">
<option>{t}No gallery{/t}</option>
{section name="i" loop=$galleries}
<option value="{$galleries[i].id}" {if $gallery_id eq $galleries[i].id}selected="selected"{/if}>{$galleries[i].title|escape:'quote'} - {$galleries[i].status} - {$galleries[i].created|date_format:$conf->date_format} - {$galleries[i].lang}</td></option>
{/section}
</select>
{/if}
</div>