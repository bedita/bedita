
{assign var='cat' value=$object.Category|default:''}
<div class="tab"><h2>{t}Media type{/t}</h2></div>
<div id="mediatypes">
	
<ul class="inline">

	<li class="ico_image {if $cat=='image'}on{/if}">
		Image <input type="radio" name="mediatype" value="image" {if $cat=='image'}checked="checked"{/if}/>
	</li>
	<li class="ico_video {if $cat=='video'}on{/if}">
		Video <input type="radio" name="mediatype" value="video" {if $cat=='video'}checked="checked"{/if}/>
	</li>
	<li class="ico_audio {if $cat=='audio'}on{/if}">
		Audio <input type="radio" name="mediatype" value="audio" {if $cat=='audio'}checked="checked"{/if}/>
	</li>
	<li class="ico_text {if $cat=='text'}on{/if}">
		Text <input type="radio" name="mediatype" value="text" {if $cat=='text'}checked="checked"{/if}/>
	</li>
	<li class="ico_spreadsheet {if $cat=='spreadsheet'}on{/if}">
		Spreadsheet <input type="radio" name="mediatype" value="spreadsheet" {if $cat=='spreadsheet'}checked="checked"{/if}/>
	</li>
	<li class="ico_presentation {if $cat=='presentation'}on{/if}">
		Presentation <input type="radio" name="mediatype" value="presentation" {if $cat=='presentation'}checked="checked"{/if}/>
	</li>
	<li class="ico_drawing {if $cat=='drawing'}on{/if}">
		Drawing <input type="radio" name="mediatype" value="drawing" {if $cat=='drawing'}checked="checked"{/if}/>
	</li>
	<li class="ico_chart {if $cat=='chart'}on{/if}">
		Chart <input type="radio" name="mediatype" value="chart" {if $cat=='chart'}checked="checked"{/if}/>
	</li>
	<li class="ico_formula {if $cat=='formula'}on{/if}">
		Formula <input type="radio" name="mediatype" value="formula" {if $cat=='formula'}checked="checked"{/if}/>
	</li>
</ul>

<br style="clear:both !important" />
	
</div>

