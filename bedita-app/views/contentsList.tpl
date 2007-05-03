{*
file include.
visualizza gli elenchi.
*}

{assign_associative var="options" url=$beurl->filterPaginatorParams()}
{assign var="par" value=$html->params}
{assign_concat var="url_content" 0='/' 1 =$par.controller 2='/content/'}

<table border="0" cellspacing="0" cellpadding="2" class="indexList">

<tr>
	<th>{$paginator->sort('titolo', null, $options)}</th>
	<th>{$paginator->sort('status', null, $options)}</th>
	<th>{$paginator->sort('data', null, $options)}</th>
	<th>{$paginator->sort('scadenza', null, $options)}</th>
	<th>{$paginator->sort('ID', null, $options)}</th>
</tr>

{foreach from=$Lists item="content"}
	{assign var="now" value=$smarty.now|date_format:"%Y%m%d"}
	<tr style="cursor:pointer;" class="{if ($content.status == 'off')}off{elseif ($content.status == 'draft')}draft{/if}" 
		onMouseOver	= "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '{$moduleSelected.Module.color}'"	
		onMouseOut 	= "this.style.backgroundColor = oldBGColor"
		>	
		<td onClick= "document.location ='./frmModify/{$content.ID}'">{$content.titolo|truncate:60:"...":true}</td>
		<td align="center">{$content.status}</td>
		<td align="center">{$content.data|date_format:"%d-%m-%Y"}</td>
		<td align="center" class="{if (!$content.valida)}scad{/if}">{$content.fine|date_format:"%d-%m-%Y"}</td>
		<td style="text-align:right"><a href="{$html->url($url_content)}{$content.ID}">{$content.ID}</a></td>
	</tr>
					
{foreachelse}
	<tr><td colspan="10" align="center"><br><br><br>nessun documento<br><br><br><br></td></tr>
{/foreach}
	</table>


