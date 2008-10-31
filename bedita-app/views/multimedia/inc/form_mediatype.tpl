{assign var='cat' value=$object.Category|default:''}
<div class="tab"><h2>{t}Media type{/t}</h2></div>
<div id="mediatypes">
	
<ul class="inline">
		
	<li class="ico_image">
		Image <input type="radio" name="mediatype" value="image" {if $cat=='image'}checked="checked"{/if}/>
	</li>
	<li class="ico_video">
		Video <input type="radio" name="mediatype" value="video" {if $cat=='video'}checked="checked"{/if}/>
	</li>
	<li class="ico_audio">
		Audio <input type="radio" name="mediatype" value="audio" {if $cat=='audio'}checked="checked"{/if}/>
	</li>
	<li class="ico_text">
		Text <input type="radio" name="mediatype" value="text" {if $cat=='text'}checked="checked"{/if}/>
	</li>
	<li class="ico_spreadsheet">
		Spreadsheet <input type="radio" name="mediatype" value="spreadsheet" {if $cat=='spreadsheet'}checked="checked"{/if}/>
	</li>
	<li class="ico_presentation">
		Presentation <input type="radio" name="mediatype" value="presentation" {if $cat=='presentation'}checked="checked"{/if}/>
	</li>
	<li class="ico_drawing">
		Drawing <input type="radio" name="mediatype" value="drawing" {if $cat=='drawing'}checked="checked"{/if}/>
	</li>
	<li class="ico_chart">
		Chart <input type="radio" name="mediatype" value="chart" {if $cat=='chart'}checked="checked"{/if}/>
	</li>
	<li class="ico_formula">
		Formula <input type="radio" name="mediatype" value="formula" {if $cat=='formula'}checked="checked"{/if}/>
	</li>
</ul>

<br style="clear:both !important" />
	
</div>

