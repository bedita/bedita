<div class="bodybg" style="padding:10px;">


<label>Cerca:</label> &nbsp; <input type="text">
&nbsp;&nbsp;
in: <select name="">
		<option></option>
		<option>albero pubblicazione</option>
	</select>
<hr>


tipo: 
<select name="type">
	<option>all</option>
		<option>document</option>
		<option>event</option>
		<option>gallery</option>
		<option>books</option>
		<option>bibliography</option>
		
	</select>
&nbsp;&nbsp;
lingua: 
<select name="type">
	<option>all</option>
		<option>english</option>
		<option>hindi</option>
		<option>aramaic</option>
		<option>vatavana</option>
		<option>bengali</option>
		
	</select>
&nbsp;&nbsp;
<input type="submit" value=" {t}Search{/t} ">
<hr />

<table class="indexlist">

		<tr>
			<th></th>
			<th>Id</th>
			<th>title</th>
			<th>type</th>
			<th>status</th>
			<th>date</th>
			<th>lang</th>
		</tr>

	
		{section name="i" loop=24}
		
		<tr>
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input  type="checkbox" name="object_chk" class="objectCheck" title="{$translations[i].LangText.id}" />
			</td>
			<td><a href="">12234</a></td>
			<td><a href="">Io sono il titolo come al solito</a></td>
			<td>
				<span style="margin:0" class="listrecent books">&nbsp;</span>
			</td>
			<td>draft</td>
			<td>12 oct 2007</td>
			<td>english</td>
		</tr>
		{/section}


</table>	
	
<hr />
	<div id="contents_nav">
		
	
					{t}Items{/t}: {$toolbar.size} | {t}page{/t} {$toolbar.page} {t}of{/t} {$toolbar.pages} 

						&nbsp; | &nbsp;
						<span><a href="javascript: void(0);" id="streamFirstPage" title="{t}first page{/t}">first</a></span>
						&nbsp; | &nbsp;
						<span><a href="javascript: void(0);" id="streamPrevPage" title="{t}previous page{/t}">prev</a></span>
			
						&nbsp; | &nbsp;			
					
						<span><a href="javascript: void(0);" id="streamNextPage" title="{t}next page{/t}">next</a></span>
						&nbsp; | &nbsp;
						<span><a href="javascript: void(0);" id="streamLastPage" title="{t}last page{/t}">last</a></span>
										


	</div>

</div>