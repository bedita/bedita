<script type="text/javascript">
{literal}
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$.validator.setDefaults({ 
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});

$(document).ready(function(){
	$('#tagdetails').show() ;
	$("#updateform").validate();
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{$html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the tag?{/t}"
		{literal}
	});
});

{/literal}
</script>


<input  type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
<div class="tab"><h2>{t}Tag details{/t}</h2></div>
<fieldset id="tagdetails">
	<table class="bordered">
		<tr>
			<th>{t}Name{/t}:</th>
			<td>
				<input type="text" name="data[label]" value="{$object.label|default:''|escape:'html'|escape:'quotes'}"
				class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Name is required (at least %1 alphanumerical char){/t}"/>
			</td>
		</tr>
		<tr>
			<th>{t}Status{/t}:</th>
			<td>
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
			</td>
		</tr>
		<tr>
			<th>{t}Occurrences{/t}:</th>
			<td>
				114
			</td>
		</tr>
		<tr>
			<th style="vertical-align:top">{t}referenced objects list{/t}:</th>
			<td>
				<ul class="bulleted">
					<li>Mi sembra un paravaneto</li>
					<li>Ma è una nerchia mostruosa</li>
					<li>Tappetti</li>
					<li>Suricati in fila per 2</li>
					<li>Sedici file di bava</li>
					<li>Solo tuuuu la la la la</li>
				</ul>
			</td>
		</tr>
	</table>

</fieldset>
