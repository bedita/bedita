{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<style>
	.update-list strong {
		font-weight: bold;
	}
</style>

<div class="main">
	<div class="update-list">
		{$beForm->csrf()}
		<table class="indexlist js-header-float">
			<thead>
				<tr>
					<th>Name</th>
					<th>Folder</th>
					<th></th>
				</tr>
			</thead>
			{if !empty($folders)}
			{foreach from=$folders item=p}
			<tr>
				<td style="white-space: nowrap">{$p.name}</td>
				<td>
					<strong>{t}Folder{/t}:</strong> {$p.path}<br />
					{if !empty($p.type)}
					<strong>{t}Revision control system{/t}:</strong> {$p.type}<br />
					{/if}
					{if !empty($p.branch)}
					<strong>{t}Branch{/t}:</strong> {$p.branch}<br />
					{/if}
					{if !empty($p.history)}
					<strong>{t}Last commit{/t}:</strong> {$p.history['hash']}<br />
					<strong>{t}on date{/t}:</strong> {$p.history['date']}<br />
					<strong>{t}by{/t}:</strong> {$p.history['author']}<br />
					<i>{$p.history['message']}</i>
					{/if}
					{if !empty($p.notice)}
					<span style="color: red"><pre>{t}{$p.notice}{/t}</pre></span><br />
					{/if}
				</td>
				<td style="text-align: right">{if $p.valid}<button class="ajax" rel="{$p.path}">{t}update{/t}</button>{/if}</td>
			</tr>
			{/foreach}
			{else}
			<tr>
				<td colspan="2">0 {t}projects found{/t}</td>
			</tr>
			{/if}
		</table>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$(document).delegate('button.ajax', 'click', function() {
		var $_this = $(this);
		var op = $_this.attr("rel");
		if (confirm('{t}Do you really want update {/t}' + op + '{t}?{/t}')) {
			var loader = $('<div class="loader">');
			loader.width(25).height(25).css('margin', '0 auto').show();
			$_this.hide().after(loader);

			var postData = {
				operation: op
			}

			postData = addCsrfToken(postData, '.update-list');

			$.ajax({
				url: "{$html->url('/admin/update')}",
				data: postData,
				dataType: "json",
				type: "post",
				error: function(jqXHR, textStatus, errorThrown) {

					alert(errorThrown);
					$_this.show();
					loader.remove();
				},
				success: function(data, textStatus, jqXHR) {

					var typeLabel = data.error != undefined ? 'Error' : 'Info';
					var type = typeLabel.toLowerCase();
					var message = ''+ 
					'<div class="message ' + type + '">' +
						'<h2>' + typeLabel + '</h2>' +
						'<p style="margin-top: 10px">' + data.message + '</p>' + 
						'<hr />' + 
						((data.details) ? '<a class="messagedetail" href="#">{t}see details{/t}</a>' : '') + 
						((data.details) ? '<div class="messageDetail" style="display: none"><pre>' + data.details + '</pre></div>' : '') + 
						'<a class="closemessage">{t}close{/t}</a>' +
					'</div>';

					$("#messagesDiv")
						.empty()
						.html(message)
						.triggerMessage(type, 5000);

					$_this.show();
					loader.remove();
				}
			});
		}
	});
});
</script>