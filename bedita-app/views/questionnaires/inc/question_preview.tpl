{*
** Preview container
** Included in layout/default.tpl
*}

<div id="preview_container_question">

	<div class="p_multiple">
		<input type="checkbox" name="p"> {t}answer{/t} 1<br />
		<input type="checkbox" name="p"> {t}answer{/t} 2<br />
		<input type="checkbox" name="p"> {t}answer{/t} 3<br />
		<input type="checkbox" name="p"> {t}answer{/t} 4<br />	
		
	</div>
	<div class="p_single_radio">
		
		<input type="radio" name="p"> {t}answer{/t} 1<br />
		<input type="radio" name="p"> {t}answer{/t} 2<br />
		<input type="radio" name="p"> {t}answer{/t} 3<br />
		<input type="radio" name="p"> {t}answer{/t} 4<br />
		<input type="radio" name="p"> {t}answer{/t} 5<br />
		
	</div>
	<div class="p_single_pulldown">

		<select>	
			<option> {t}answer{/t} 1</option>
			<option> {t}answer{/t} 2</option>
			<option> {t}answer{/t} 3</option>
			<option> {t}answer{/t} 4</option>
			<option> {t}answer{/t} 5</option>
		</select>
		
	</div>
	<div class="p_freetext">
		
		Write your answer. Limit is N characters (white spaces included)
		<textarea style="width:280px">{t}answer{/t} 1</textarea>
		
	</div>
	<div class="p_checkopen">
		
		<input type="checkbox" name="p"> {t}answer{/t} 1, specify: <input type="text" /> <br />
		<input type="checkbox" name="p"> {t}answer{/t} 2, specify: <input type="text" /> <br />
		<input type="checkbox" name="p"> {t}answer{/t} 3, specify: <input type="text" /> <br />
		<input type="checkbox" name="p"> {t}answer{/t} 4, specify: <input type="text" /> <br />
		<input type="checkbox" name="p"> {t}answer{/t} 5, specify: <input type="text" /> <br />
		
	</div>
	<div class="p_degree">
		
		<input type="checkbox" name="p"> {t}answer{/t} 1 <select name=""><option>1</option><option>2</option><option>3</option><option>4</option></select><br />
		<input type="checkbox" name="p"> {t}answer{/t} 2 <select name=""><option>1</option><option>2</option><option>3</option><option>4</option></select><br />
		<input type="checkbox" name="p"> {t}answer{/t} 3 <select name=""><option>1</option><option>2</option><option>3</option><option>4</option></select><br />
		<input type="checkbox" name="p"> {t}answer{/t} 4 <select name=""><option>1</option><option>2</option><option>3</option><option>4</option></select><br />
		

	</div>

</div>

