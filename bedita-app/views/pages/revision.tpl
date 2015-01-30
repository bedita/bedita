{$html->css('bedita4Modal')}

<script>	

	$('#restoreselected').click(function (){
		
		$('.version input:checked').each(function(){
			
			// select appropriate target and source value
			var fieldid = $(this).val();
			var content = $('.revision#'+fieldid+'').html();
			var target = $('#updateForm *[name="data['+fieldid+']"]');
			var bgColor = '#ffccff';
			
			// change target content
			target.val(content).css('backgroundColor',bgColor).parents('fieldset').prev('.tab').BEtabsopen();
			
			if (target.hasClass('mce') || target.hasClass('mceSimple')) {
				if (window['tinyMCE']) {
					var tinyMceInstance = tinyMCE.get('data['+fieldid+']');
					if (tinyMceInstance == undefined) {
						tinyMceInstance = tinyMCE.get(fieldid);
					}
					if (tinyMceInstance != undefined) {
						tinyMceInstance.setContent(content);
						tinyMceInstance.getBody().style.backgroundColor = bgColor;
					}
				} else {
					if (window['CKEDITOR']) {
						var ckeditorInstance = CKEDITOR.instances['data['+fieldid+']'];
						if (ckeditorInstance == undefined) {
							ckeditorInstance = CKEDITOR.instances[fieldid];
						}
						if (ckeditorInstance != undefined) {
							ckeditorInstance.setData(content);
							$(ckeditorInstance.container.$).find('iframe')[0].contentDocument.body.style.backgroundColor = bgColor;
						}
					}
				}
			}
			
			//set page on to save/confirm on leave status
			$('.secondacolonna .modules label').addClass('save').prop('title', 'unsaved object');

			/*
			// da fare*******
			// 
			// i campi select e radio..
			// testare sulel card
			*/

		}); 
		
		$('.close').click();
	
	});
	
</script>

<div style="padding: 10px;">
	<table class='version bordered'>
	<thead>
		<tr>
			<td colspan=5>
				{t}Version{/t} <b>{$version.revision}</b> / <b>{$totRevision}</b>, 
				{t}created by{/t} <b>{$user.realname|default:''|escape} [{$user.userid|default:''|escape}]</b>
				{t}on{/t} <span class='evidence'>{$version.created|date_format:$conf->dateTimePattern}</span>
				
				<!-- <input type='button' class='BEbutton' id='restoreall' style='margin-left:10px' value='{t}restore all{/t}' /> -->
				<input type='button' class='BEbutton' id='restoreselected' style='margin-left:10px' value='{t}restore selected{/t}' /> 
			</td>
		</tr>
	</thead>
	<tbody>
	{foreach from=$diff item=xdiff key=key}
		<tr>
			<td style='width:20px'><input type='checkbox' value='{$key}' /></td>
			<th nowrap>{t}{$tr->moduleField($moduleName, $key)}{/t}</th>
			<td class='revision' id='{$key}'>{$revision[$key]|default:''}</td>
			{*<td>{$diff}</td>*}
		</tr>
	{/foreach}
	</tbody>
	</table>
</div>
