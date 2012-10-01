<ul data-role="listview" data-filter="true" {*data-inset="true"*}>
		{foreach from=$objects item=object}
		<li>
				<a href="{$this->Html->url('view/')}{$object.id}">
						<h3>{$object.title|truncate:64|default:"<i>[no title]</i>"}</h3>
						<p>{t}status{/t}: <strong>{$object.status}</strong>, {t}language{/t}: <strong>{$object.lang}</strong>, {t}comments{/t}: <strong>{$object.num_of_comment|default:0}</strong></p>
						<p>{t}last modified{/t}: <strong>{$object.modified|date_format:$conf->dateTimePattern}</strong></p>
				</a>
		</li>
		{/foreach}
		{if ($this->BeToolbar->current() < $this->BeToolbar->pages())}
		<li class="ui-body ui-body-b">
				<fieldset class="ui-grid-solo">
						<div class="ui-block-a"><a id="moreButton" href="{$this->Html->url('/')}{$currentModule.url}/index/page:{$this->BeToolbar->current()+1}" data-role="button">{t}More{/t}</a></div>
				</fieldset>
		</li>
		{/if}
</ul>
<script>
		$(document).bind('pageinit',function(e,ui){
				// Infinityscroll
				$('#moreButton').on('click',function(e){
						e.preventDefault();
						var moreButton = $(this);
						$.mobile.showPageLoadingMsg(); // show loading spinner
						
						$('<div style="display:none"/>').appendTo('body').load(
								$(this).attr('href') + ' ul[data-role=listview] li', // load li from next page
								function(){						
										var newMoreButton = $('#moreButton',this);
										
										if (newMoreButton.length){
												var nextUrl = newMoreButton.attr('href'); // get next page's url 
												newMoreButton.parents('li').remove(); // remove newMoreButton's li
												moreButton.attr('href', nextUrl); // update href in our moreButton
										}
										
										var newObjects = $(this).children(); // lis
										$('ul[data-role=listview]')
												.append(newObjects,moreButton.parents('li'))
												.listview('refresh'); // refresh our list
										
										$(this).remove(); // remove conainer div in body
										$.mobile.hidePageLoadingMsg(); // hide loading spinner
								}
						);
				});
		});
</script>