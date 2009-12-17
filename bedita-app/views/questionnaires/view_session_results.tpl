{*
** document view template
*}
{$html->css("ui.datepicker", null, null, false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}
{literal}
<script type="text/javascript">
    $(document).ready(function(){
		
		openAtStart("#answers");
		
    });
</script>
{/literal}

{$view->element('form_common_js')}

	
    {$view->element('modulesmenu')}
    
	{include file="inc/menuleft.tpl" method="viewQuestion"}
    
	<div class="head">
		
        <h1>Maria Balditto <span style="font-size:0.75em">answers at</span> «Titolo del questionario»</h1>
		<br />
		completed on <b>23 oct 2009 </b>at <b>12.24.15</b>
		
    </div>

	{include file="inc/menucommands.tpl" method="viewResults" fixed = true}

<form action="{$html->url('/forms/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	<div class="main">
		
   			<div class="tab"><h2>{t}Summary{/t}</h2></div>
			<fieldset id="summary">
					
			</fieldset>
			
			<div class="tab"><h2>{t}Questions and answers{/t}</h2></div>
			<fieldset id="answers">
				<ol style="margin-left:10px;">
					<li style="margin-bottom:10px; border-bottom:1px solid gray; padding-bottom:10px;">
						<h2>Qual'è la distanza tra la Terra e Alfa Centauri?</h2>
						<p class="graced" style="color:#666;">
						E' la stella più vicina al sistema solare, breve descrizione della domanda max 255 car. poi detaglio se caso ll omanda completa</p>
						<ul style="margin-left:0px;">
							<li style="margin-top:5px">
								<input disabled type="radio" /><label style="font-weight:normal; margin-left:5px;">40 miliardi di Km</label>
							</li>
							<li style="margin-top:5px">
								<input disabled checked type="radio" /><label style="color:red; margin-left:5px;">950 milioni Km</label>
								<span style="margin-left:20px; color:red"> error </span>
							</li>
							<li style="margin-top:5px">
								<input disabled type="radio" /><label style="font-weight:normal; margin-left:5px;">16 miliardi di Km</label>
							</li>
						</ul>	
					</li>
					<li>
						<h2>Quant'è calcolata la temperatura sulla superficie del sole?</h2>
						<p class="graced" style="color:#666;"></p>
						<ul style="margin-left:0px;">
							<li style="margin-top:5px">
								<input disabled type="radio" /><label style="font-weight:normal; margin-left:5px;">380 K</label>
							</li>
							<li style="margin-top:5px">
								<input disabled  type="radio" /><label style="font-weight:normal; margin-left:5px;">2522 K</label>
				
							</li>
							<li style="margin-top:5px">
								<input disabled checked type="radio" /><label style="color:green; margin-left:5px;">5785 K</label>
								<span style="margin-left:20px; color:green"> correct </span>
							</li>
						</ul>	
					</li>
				</ol>	
			</fieldset>

   			<div class="tab"><h2>{t}User details{/t}</h2></div>
			<fieldset id="user">
					
			</fieldset>
			
   			<div class="tab"><h2>{t}Notes{/t}</h2></div>
			<fieldset id="editor">
					
			</fieldset>
		
    </div>
</form>

{$view->element('menuright')}