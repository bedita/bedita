{*
file include.
visualizza gli elenchi.
*}
{php}
$vs = &$this->get_template_vars() ;
//pr($vs["Authors"]);
//exit;
{/php}

		<table border="0" cellspacing="0" cellpadding="2" class="indexList">
		
		<tr>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=nome{if ($smarty.get.order=="titolo")}+DESC{/if}">nome</th>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=cognome{if ($smarty.get.order=="titolo")}+DESC{/if}">cognome</th>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=status{if ($smarty.get.order=="status")}+DESC{/if}">status</th>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=data{if ($smarty.get.order=="data")}+DESC{/if}">data</th>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=fine{if ($smarty.get.order=="fine")}+DESC{/if}">scadenza</th>
			<th><a href="{$selfPlus}&amp;pag=1&amp;order=ID{if ($smarty.get.order=="ID")}+DESC{/if}">ID</th>
		</tr>

{section name=i loop=$Lists.items}
	{assign var="content" value=$Lists.items[i]}
	
{assign var="now" value=$smarty.now|date_format:"%Y%m%d"}
	<tr style="cursor:pointer;" class="{if ($content.status == 'off')}off{elseif ($content.status == 'draft')}draft{/if}" 
		onMouseOver	= "oldBGColor=this.style.backgroundColor; this.style.backgroundColor = '#3399CC'"	
		onMouseOut 	= "this.style.backgroundColor = oldBGColor"
		>	
		<td onClick= "document.location ='./frmModify/{$content.ID}'">{$content.nome|truncate:60:"...":true}</td>
		<td onClick= "document.location ='./frmModify/{$content.ID}'">{$content.cognome|truncate:60:"...":true}</td>
		<td align="center">{$content.status}</td>
		<td align="center">{$content.data|date_format:"%d-%m-%Y"}</td>
		<td align="center" class="{if (!$content.valida)}scad{/if}">{$content.fine|date_format:"%d-%m-%Y"}</td>
		<td style="text-align:right"><a href="content.php?ID={$content.ID}">{$content.ID}</a></td>
	</tr>
					
{sectionelse}
	<tr><td colspan="10" align="center"><br><br><br>nessun documento<br><br><br><br></td></tr>
{/section}
	</table>


