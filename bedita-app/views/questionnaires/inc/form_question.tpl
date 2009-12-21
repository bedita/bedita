{*
** form question template
*}

{if ($conf->mce|default:true)}
	
	{$javascript->link("tiny_mce/tiny_mce", false)}
	{$javascript->link("tiny_mce/jquery.tinymce", false)}
	{$javascript->link("tiny_mce/tiny_mce_BEquestions_init", false)}
	{$javascript->link("tiny_mce/tiny_mce_BEanswers_init", false)}

{elseif ($conf->wymeditor|default:true)}

	{$javascript->link("wymeditor/jquery.wymeditor.pack", false)}
	{$javascript->link("wymeditor/wymeditor_default_init", false)}

{/if}


{literal}
<script type="text/javascript">
			
	function multiplePreview() {
		var formElement = "<input type=\"checkbox\" name=\"p\">";
		var htmlBlock = "";
		$("#answers textarea").each(function() {
			htmlBlock += formElement + " " + $(this).val() + "<br/>";
		});
		return htmlBlock;
	}

	function single_radioPreview() {
		var formElement = "<input type=\"radio\" name=\"p\">";
		var htmlBlock = "";
		$("#answers textarea").each(function() {
			htmlBlock += formElement + " " + $(this).val() + "<br/>";
		});
		return htmlBlock;
	}

	function single_pulldownPreview() {
		var htmlBlock = "";
		$("#answers textarea").each(function() {
			htmlBlock += "<option> " + $(this).val() + "</option>";
		});
		return "<select>" + htmlBlock + "</select>";
	}

	function freetextPreview() {
		var maxChars = $("input[name='data[max_chars]']").val();
		var introtext = "{/literal}{t}Write your answer. Max chars is{/t}{literal} " + maxChars;
		var htmlBlock = "<textarea style=\"width:280px;\"></textarea>";
		return introtext + htmlBlock;
	}

	function checkopenPreview() {
		var checkElement = "<input type=\"checkbox\" name=\"p\">";
		var textElement = "<input type=\"text\" />";
		var htmlBlock = "";
		$("#answers textarea").each(function() {
			htmlBlock += checkElement + " " + $(this).val() + ", {/literal}{t}specify{/t}{literal}:" + textElement + "<br/>";
		});
		return htmlBlock;
	}

	function degreePreview() {
		var checkElement = "<input type=\"checkbox\" name=\"p\">";
		var selectElement = "<select name=\"\"><option>1</option><option>2</option><option>3</option><option>4</option></select><br />"
		var htmlBlock = "";
		$("#answers textarea").each(function() {
			htmlBlock += checkElement + " " + $(this).val() + selectElement;
		});
		return htmlBlock;
	}
	
	function showAnswers() {
		$(".answers div").hide();
		$(".answers div :input").attr("disabled", "disabled");
		$(".answers div :checkbox").attr("disabled", "disabled");
		var kind = $("#tipoID option:selected").val();
		$("." + kind).show();
		$("." + kind + " :input").attr("disabled", "");
		$("." + kind + " :checkbox").attr("disabled", "");
	}	
	
	//preview functions
	$(document).ready(function(){
		$("#tipoID").change(function () {
			showAnswers();
			$('#preview_container_question').hide();
			$('#preview').val("EXAMPLE");
		});
			
		$("#preview").toggle( 
		function () 
		{ 
			var qtype = $('#tipoID OPTION:selected').val();
			$('#preview').toggleValue("CLOSE EXAMPLE","EXAMPLE");
			$('#preview_container_question DIV').not('.p_'+qtype).toggle();
			var htmlBlock = window[qtype + "Preview"]();
			$('#preview_container_question .p_' + qtype).append(htmlBlock);
			$('#preview_container_question').toggle();
		    $('#tipoID').attr("disabled", true);
		}, 
		function () 
		{ 
			var qtype = $('#tipoID OPTION:selected').val();
			$('#preview').toggleValue("CLOSE EXAMPLE","EXAMPLE");
			$('#preview_container_question DIV').not('.p_'+qtype).toggle();
			$('#preview_container_question').toggle();
		    $('#tipoID').removeAttr("disabled");
		    //window[qtype + "Preview"](false);
		    $('#preview_container_question .p_' + qtype).empty();
		});
	});
</script>
{/literal}

<form action="{$html->url('/questionnaires/saveQuestion')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
<input type="hidden" name="data[object_type_id]" value="{$conf->objectTypes.question.id}"/>

   <div class="tab"><h2>{t}Question detail{/t}</h2></div>

		<fieldset id="question">
			
		<table class="bordered">
			<tr>
				<th>{t}title{/t}:</th>
				<td>
					<input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject" />
				</td>
			</tr>
		
			<tr>
				<th>{t}text{/t}:</th>
				<td>
					<textarea id="subtitle" style="width:380px; height:80px" class="shortdesc mce" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
					
				</td>
			</tr>
			
			<tr>
				<th>{t}type{/t}:</th>
				<td>
					<select name="data[question_type]" id="tipoID">
					{foreach from=$conf->questionTypes item="label" key="value"}
						<option value="{$value}"{if $value == $object.question_type|default:""} selected{/if}>{t}{$label}{/t}</option>
					{/foreach}
					</select>
															
					&nbsp; <input type="button" value="EXAMPLE" id="preview" class="BEbutton" />
				
				</td>
			</tr>
			
		<tr>
			<td colspan=2 style="padding:0px; border:0px">
			
			{include file="./inc/question_preview.tpl"}	
			
			</td>
		</tr>

		<tr>
			<td colspan=2 class="answers">
				
			{include file="./inc/answers_list.tpl"}	
				
			</td>
		</tr>
		
		<tr>
			<th>{t}edu level{/t}:</th>
			<td>
				<select name="data[edu_level]" id="eduLevel">
					<option value="">--</option>
				{foreach from=$conf->eduLevel item="label" key="value"}
					<option value="{$value}"{if $value == $object.edu_level|default:""} selected{/if}>{t}{$label}{/t}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<th>{t}difficulty{/t}:</th>
			<td>
				<select name="data[question_difficulty]" id="difficulty">
					<option value="">--</option>
				{foreach from=$conf->questionDifficulty item="label" key="value"}
					<option value="{$value}"{if $value == $object.question_difficulty|default:""} selected{/if}>{t}{$label}{/t}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		</table>
	</fieldset>

   <div class="tab"><h2>{t}Question suggestions{/t}</h2></div>

	<fieldset id="suggestions">	
		<table class="bordered">
		<tr>
			<th>{t}If  error{/t}:</th>
			<td>
				<textarea id="subtitle" style="width:380px; height:80px" class="mce" name="data[text_fail]">{$object.text_fail|default:""}</textarea>
			</td>
		</tr>
		<tr>
			<th>{t}If correct{/t}:</th>
			<td>
				<textarea id="subtitle" style="width:380px; height:80px" class="mce" name="data[text_ok]">{$object.text_ok|default:""}</textarea>
			</td>
		</tr>
		</table>
	
	</fieldset>

   <div class="tab"><h2>{t}Question properties{/t}</h2></div>

	<fieldset id="properties">	
			
		<table class="bordered">
			<tr>
		
				<th>{t}status{/t}:</th>
				<td colspan="4">
					{if $object.fixed}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[fixed]" value="1" />
						<input type="hidden" name="data[status]" value="{$object.status}" />
					{else}

						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}

					{/if}
				</td>
		
			</tr>
			<tr>
				<th>{t}included in questionnaires{/t}:</th>
				<td>
					<ul>
					{foreach from=$object.RelatedObject item=item}
					{if ($item.switch == "question")}
						<li>&bull; <a title="{$item.object_id}" href="/view/{$item.object_id}">{$item.object_id}</a></li>
					{/if}
					{/foreach}
					</ul>
				</td>
				
			</tr>
		
			<tr>
				<th>{t}author{/t}:</th>
				<td>
					<input type="text" name="data[creator]" value="{$object.creator}" />
				</td>
			</tr>
		
		
			<tr>
				<th>{t}language{/t}:</th>
				<td>
				{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
				<select name="data[lang]" id="main_lang">
					{foreach key=val item=label from=$conf->langOptions name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
					{foreach key=val item=label from=$conf->langsIso name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
				</select>
				</td>
			</tr>
		
		</table>
	
	</fieldset>

	{assign_associative var="params" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}
	{$view->element('form_file_list', $params)}

	{$view->element('form_tags')}
		
	{$view->element('form_translations')}

	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}

	
	
</form>