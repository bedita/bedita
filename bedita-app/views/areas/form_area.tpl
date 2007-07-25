{*
template incluso.
Visualizza il form di un' Area.
*}
{php}$vs = &$this->get_template_vars() ;{/php}


	<div id="containerPage">
		{formHelper fnc="create" args="'updateform', array('id' => 'updateform', 'action' => '/areas/saveArea', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}
		<div class="FormPageHeader">
			<h1>{$area.title|default:"nuova area"}</h1>
	
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<a id="openAllBlockLabel" style="display:block" href="javascript:showAllBlockPage(1)"><b>&#155; </b>apri tutti i dettagli</a>
						<a id="closeAllBlockLabel" href="javascript:hideAllBlockPage()"><b>&#155; </b>chiudi tutti i dettagli</a>
					</td>
					<td style="padding-left:40px">
						{formHelper fnc="submit" args="' salva ', array('name' => 'save', 'class' => 'submit')"}
					</td>
					<td style="padding-left:40px">
						&nbsp;
					</td>
				</tr>
			</table>
		</div>

		<h2 class="showHideBlockButton">Propriet&agrave;</h2>
		<div class="blockForm" id="proprieta" style="display:block;">

			<b>status</b>:
			{html_radios name="status" options=$conf->statusOptions selected=$area.status|default:$conf->status separator=" "}
			<hr>
				<table class="tableForm" border="0">
					<tr id="">
						<td class="label">Lingua:</td>
						<td>
							<select name="lang">
								{html_options options=$conf->langOptions selected=$area.lang|default:$conf->lang}
							</select>							
						</td>
						<td>
						&nbsp;
						</td>
					</tr>

					<tr id="Title_TR_{$area.lang|default:$conf->lang}">
						<td class="label">Titolo:</td>
						<td>
							<input  class="inputText" type="text" name="title" value="{$area.title|default:''|escape:'html'|escape:'quotes'}" >&nbsp;
						</td>
						{if ($area)}					
						<td>
							<input class="cmdField" type="button" value="lang ..." onclick="addLang('{$area.title|default:''|escape:'quotes'}', '{$area.lang|default:$conf->lang}')">
						</td>
						{/if}
					</tr>
				</table>
				{if ($area)}
				<hr>
				<table class="tableForm" border="0">
					<tr>
						<td class="label">Alias:</td>
						<td>{$area.nickname}</td>
					</tr>
					<tr>
						<td class="label">Creata il:</td>
						<td>{$area.created|date_format:$conf->date_format}</td>
					</tr>
					<tr>
						<td class="label">Ultima modifica:</td>
						<td>{$area.modified|date_format:$conf->date_format}</td>
					</tr>
					<tr>
						<td class="label">IP:</td>
						<td>{$area.IP_created}</td>
					</tr>
				</table>
			{/if}
		</div>
		
		<h2 class="showHideBlockButton">Propriet&agrave; Custom</h2>
		<div class="blockForm" id="proprietaCustom">
			{include file="../pages/form_custom_properties.tpl" el=$area}
		</div>

		<h2 class="showHideBlockButton">Permessi</h2>
		<div class="blockForm" id="permessi">
		Permessi
		</div>

		</form>			
	</div>

