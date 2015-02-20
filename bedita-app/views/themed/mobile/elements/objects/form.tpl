	{strip}
	<form action="{$html->url('/')}{$submiturl}/save" method="post" name="updateForm" id="updateForm" class="cmxform">
		{$beForm->csrf()}
		<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

		<ul data-role="listview">
			{* Title *}
			<li data-role="list-divider">{t}Title{/t}</li>			
			<li data-role="fieldcontain">
				<label for="data[title]">{t}Title{/t}:</label>
				<input type="text" id="data[title]" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject"/>
			</li>
			
			<li data-role="fieldcontain">
				<label for="data[description]">{t}Description{/t}:</label>
				<textarea id="data[description]" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
			</li>
			
			<li data-role="fieldcontain">
				<label for="data[nickname]">{t}Unique name{/t} ({t}url name{/t}):</label>
				<input type="text" id="data[nickname]" name="data[nickname]" style="font-style:italic;" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>
			</li>

			{* Text *}
			<li data-role="list-divider">{t}Text{/t}</li>
			{if (!empty($addshorttext)) or (!empty($object.abstract))}
			<li data-role="fieldcontain">
				<label for="data[abstract]">{t}short text{/t}:</label>
				<textarea id="data[abstract]" name="data[abstract]">{$object.abstract|default:''}</textarea>
			</li>
			{/if}
			<li data-role="fieldcontain">
				<label for="data[body]">{t}long text{/t}:</label>
				<textarea id="data[body]" name="data[body]" rows="10">{$object.body|default:''}</textarea>
			</li>
			
			{* Position *}
	
			<li data-role="list-divider">{t}Position{/t}</li>
			<li data-role="fieldcontain">
				<label for="data[destination][]">{t}select position{/t}:</label>
				<select id="data[destination][]" name="data[destination][]" multiple="multiple" data-native-menu="false">
					{assign_associative var="params" parentIds=$parents}
					<option value="">{t}None{/t}</option>
					{$beTree->optionsMobile($tree, $params)}
				</select>
			</li>
			
				
			{* Properties *}
			<li data-role="list-divider">{t}Properties{/t}</li>
			{if in_array('administrator',$BEAuthUser.groups)}
			<li data-role="fieldcontain">
				
				<label for="data[fixed]">{t}Fixed{/t}:</label>
				<select id="data[fixed]" name="data[fixed]" data-role="slider">
					<option value="0" {if empty($object.fixed)}selected{/if}>off</option>
					<option value="1" {if !empty($object.fixed)}selected{/if}>on</option>
				</select>
			</li>
			{else}
				<input type="hidden" name="data[fixed]" value="{$object.fixed}" />
			{/if}
			
			<li data-role="fieldcontain">
				<fieldset data-role="controlgroup">	
					<legend for="data[status]">{t}Status{/t}:</legend>
					{if $object.fixed}
						<p>{t}This object is fixed - some data is readonly{/t}</p>
						<input type="hidden" name="data[status]" value="{$object.status}" />
					{else}
						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus}
					{/if}
				</fieldset>
			</li>

			{* Buttons *}
			<li class="ui-body ui-body-b">
				<fieldset class="ui-grid-a">
					<div class="ui-block-a"><button id="deleteButton" type="submit" data-theme="f" value="{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}">{t}Delete{/t}</button></div>
					<div class="ui-block-b"><button id="saveButton" type="submit" data-theme="a" value="{$html->url('/')}{$submiturl}/save">{t}Save{/t}</button></div>
				</fieldset>
			</li>

			{* Actions *}
			{*
			<li data-role="list-divider">{t}Actions{/t}</li>
			<li data-role="fieldcontain">
			    <fieldset data-role="controlgroup">
			    	<legend>Choose an action:</legend>
			         	<input type="radio" name="action" id="saveBEObject" value="{$html->url('/')}{$submiturl}/save" checked="checked" />
			         	<label for="saveBEObject">{t}Save{/t}</label>

								<input type="radio" name="action" id="cloneBEObject" value="{$html->url('/')}{$submiturl}/cloneObject" />
			         	<label for="cloneBEObject">{t}Clone{/t}</label>

								<input type="radio" name="action" id="delBEObject" value="{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}"/>
			         	<label for="delBEObject" style="color:#f00;">{t}Delete{/t}</label>
			    </fieldset>
			</li>
			<li data-role="fieldcontain" data-theme="b">
				<button type="submit" data-theme="a">{t}Submit{/t}</button>
			</li>
			*}
		</ul>

	</form>
	{/strip}
	<script>
		$(document).bind('pageinit',function(e,ui){
			// Manage form submission based on action
			
			$('#deleteButton').click(function(e){
				if (confirm("{t}Are you sure you want to delete{/t}")) {
					$('#updateForm').prop('action', $(this).val()).submit();
				} else {
					e.preventDefault();
				}
			});
			
			$('#saveButton').click(function(e){
				$('#updateForm').prop('action', $(this).val()).submit();
			});

		});
	</script>
	{*
	<script>
		$(document).bind('pageinit',function(e,ui){
			// Manage form submission based on action
			$('#updateForm').on('submit',function(e){
				$(this).prop('action', $("input[name='action']:checked").val());
				if ($('#cloneBEObject:checked').length){ // Clone Object
					var cloneTitle=prompt("{t}Title{/t}",$("input[name='data[title]']").val()+"-copy",false);
					if (cloneTitle) {
						$("input[name='data[title]']").val(cloneTitle);
					} else {
						e.preventDefault();
					}
				}
			});
		});
	</script>
	*}