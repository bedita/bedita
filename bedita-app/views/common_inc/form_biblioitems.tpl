
<div class="tab"><h2>{t}Books{/t}</h2></div>

<fieldset id="books">
	


{section name=b loop=5}

<table class="bordered" style="border:1px solid gray; margin-bottom:20px;">
	<tr>
		<td style="width:33%">
			Musil,
			<br />
			<i>L'uomo senza qualità</i>, 
			<br />Einaudi, Torino, 2006
			<br />
			isbn: 8233-9636-2333
		</td>
		<td style="padding:0px; vertical-align:top;">
			<div style="padding:4px; height:135px; overflow:auto">
			io sono il testo della scheda bibliografica, 
			mi accompagno ai dati veri e propri del libro.
			(se il libro esiste come ogetto in "books", questi dati - copertina, titolo, autore, editore, anno - vengono presi direttamente da lì)
			In sostanzami accompagno ai dati veri e propri del libro.
			(se il libro esiste come ogetto in "books", questi dati - copertina, titolo, autore, editore, anno - vengono presi direttamente da lì)
			In sostanza dovrebbero essere delel relazioni di tipo bilioitem? tra doc e books, una raccolta di books, bho
			</div>
		</td>
		
	</tr>
	<tr>
		<td>

			<input class="BEbutton link" href="/books/view/923" name="details" type="button" value="details">
			<input class="BEbutton" name="remove" type="button" value="remove">
		</td>
		<td>
			<input class="BEbutton" name="edit" type="button" value="edit description">
		</td>
		
	</tr>

</table>	

{/section}


<ul class="htab">
	<li rel="submitnew">{t}write new item{/t}</li>
	<li rel="addbycode">{t}add by code{/t}</li>
	<li rel="repositoryBooks" id="reposBooks">{t}select from books repository{/t}</li>
</ul>

<div class="htabcontainer" id="addbiblioitems">

	<div class="htabcontent" id="submitnew">
		
		<table>
		<tr>
			<th>Titolo</th><td><input type="text" name="title" value=""></td>
			<th>Description</th>
		</tr>
		<tr>
			<th>Autore</th><td><input type="text" name="author" value=""></td>
			<td rowspan="4">
				<textarea style="height:150px" name="description"></textarea>
			</td>
		</tr>
		<tr>
			<th>Luogo e anno</th><td><input type="text" name="place" value=""></td>
		</tr>
		<tr>
			<th>Isbn/issn/bid</th><td><input type="text" name="code" value=""></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;"><input type="submit" value="   add   "></td>
		</tr>
		</table>
	
	</div>

	<div class="htabcontent" id="addbycode">
		<label>Insert code string </label>
		<input type="text" name="code" value="">
		<p  style="margin-top:10px">
		<label>and get data </label><select>
			<option>from SEBINA by ISBN/ISSN</option>
			<option>from SEBINA by BID</option>
			<option>from web by isbn</option>	
		</select>
		
		<input type="button" value="   search   ">
		</p>
		<hr />
		<h3>Result:</h3>
		Title: Io sono il titolo,
		<br />
		Author: io sono l'autore
		<br />
		Publisher: io le'editore, year: 2008
		<hr />
		<input type="button" value="   add   ">
	</div>
	
	<div class="htabcontent" id="repositoryBooks">
		<table>
			<tr>
				<th>Cerca</th>
				<td>
					<input type="checkbox"> autore
					<input type="checkbox"> titolo
					<input type="checkbox"> editore
					<input type="checkbox"> codice (ISBN/ISSN/BID)
				</td>
			</tr>
			<tr>
				<td></td>
					<td>
					<input type="text" name="search" value="">&nbsp;
					<input id="qw" type="button" rel="{$html->url('/')}books/listAllBooks" class="modalbutton" value="  go  ">
				</td>
			</tr>
		</table>
		
	</div>
	
</div>

</fieldset>