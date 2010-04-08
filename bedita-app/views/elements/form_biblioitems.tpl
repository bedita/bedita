
<div class="tab"><h2>{t}Books{/t}</h2></div>

<fieldset id="books">
	

{section name=b loop=5}

{bedev}
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
			<input class="BEbutton golink" href="/books/view/923" name="details" type="button" value="details">
			<input class="BEbutton" name="remove" type="button" value="remove">
		</td>
		<td>
			<input class="BEbutton" name="edit" type="button" value="edit description">
		</td>
		
	</tr>

</table>
{/bedev}

{/section}


<table class="htab">
	<td rel="submitnew">{t}write new item{/t}</td>
	<td rel="addbycode">{t}add by code{/t}</td>
	<td rel="repositoryBooks" id="reposBooks">{t}select from books repository{/t}</td>
</table>

<div class="htabcontainer" id="addbiblioitems">

	<div class="htabcontent" id="submitnew">
		
		<table>
		<tr>
			<th>{t}Title{/t}</th><td><input type="text" name="title" value=""></td>
			<th>{t}Description{/t}</th>
		</tr>
		<tr>
			<th>{t}Author{/t}</th><td><input type="text" name="author" value=""></td>
			<td rowspan="4">
				<textarea style="height:150px" name="description"></textarea>
			</td>
		</tr>
		<tr>
			<th>{t}Place and year{/t}</th><td><input type="text" name="place" value=""></td>
		</tr>
		<tr>
			<th>{t}Isbn/issn/bid{/t}</th><td><input type="text" name="code" value=""></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;"><input type="submit" value="   {t}add{/t}   "></td>
		</tr>
		</table>
	
	</div>

	<div class="htabcontent" id="addbycode">
		<label>{t}Insert code string{/t}</label>
		<input type="text" name="code" value="">
		<p  style="margin-top:10px">
		<label>{t}and get data{/t} </label><select>
			<option>{t}from{/t} SEBINA {t}by{/t} ISBN/ISSN</option>
			<option>{t}from{/t} SEBINA {t}by{/t} BID</option>
			<option>{t}from{/t} web {t}by{/t} isbn</option>	
		</select>
		
		<input type="button" value="   {t}search{/t}   ">
		</p>
		{bedev}
		<hr />
		<h3>{t}Result{/t}:</h3>
		Title: Io sono il titolo,
		<br />
		{t}Author{/t}: io sono l'autore
		<br />
		{t}Publisher{/t}: io le'editore, year: 2008
		<hr />
		<input type="button" value="   {t}add{/t}   ">
		{/bedev}
	</div>
	
	<div class="htabcontent" id="repositoryBooks">
		<table>
			<tr>
				<th>{t}Search{/t}</th>
				<td>
					<input type="checkbox"> {t}author{/t}
					<input type="checkbox"> {t}title{/t}
					<input type="checkbox"> {t}editor{/t}
					<input type="checkbox"> {t}code (ISBN/ISSN/BID){/t}
				</td>
			</tr>
			<tr>
				<td></td>
					<td>
					<input type="text" name="search" value="">&nbsp;
					<input id="qw" type="button" rel="{$html->url('/')}books/listAllBooks" class="modalbutton" value="  {t}go{/t}  ">
				</td>
			</tr>
		</table>
		
	</div>
	
</div>

</fieldset>