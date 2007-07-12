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

		<h2 class="showHideBlockButton" onClick="$('#proprieta').toggle()">Propriet&agrave;</h2>
		<div class="blockForm" id="proprieta">
		Propriet&agrave;
		</div>
		
		<h2 class="showHideBlockButton" onClick="$('#proprietaCustom').toggle()">Propriet&agrave; Custom</h2>
		<div class="blockForm" id="proprietaCustom">
		Propriet&agrave; Custom
		</div>

		<h2 class="showHideBlockButton" onClick="$('#permessi').toggle()">Permessi</h2>
		<div class="blockForm" id="permessi">
		Permessi
		</div>

		</form>			
	</div>

