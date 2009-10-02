{if empty($object) && !empty($section.currentContent)}
	{assign var="object" value=$section.currentContent}
{/if}



{if !empty($object.Comment)}
	
	{foreach from=$object.Comment item="comment" name="fc_com"}

		{if $smarty.foreach.fc_com.index == 0}
			<h3 style="margin-top:30px;">{$smarty.foreach.fc_com.total}&nbsp; 
			{if $smarty.foreach.fc_com.total == 1}{t}Comment{/t}{else}{t}Comments{/t}{/if}</h3>
		{/if}
		
		<a name="comment-{$comment.id}"></a>
		<div class=commentContainer>
			<h3>
			{if !empty($comment.url)}
				<a href="{$comment.url}" target="_blank">{$comment.author}</a>
			{else}
				{$comment.author}
			{/if}
			</h3>
			
			<p>{$comment.created|date_format:$conf->datePattern}</p>
			
			<p>{$comment.description|nl2br}</p>
		</div>
	{/foreach}

{/if}

{if $object.comments != "off"}

	{if !empty($showForm)}{$view->element('form_comments')}{/if}
	
{else}
	{if $section.currentContent.num_of_comment|default:0 > 0}<hr/>{/if}
	<span style="color: red">{t}This item is closed to comments{/t}</span> 

{/if}