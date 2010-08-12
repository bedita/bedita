{if empty($object) && !empty($section.currentContent)}
	{assign var="object" value=$section.currentContent}
{/if}

{if $object.comments != "off"}

	{if !empty($object.Comment)}
		
		<h3 id="comments-title">{$object.num_of_comment|default:0}
		{t}Response to{/t} <em>{$object.title}</em></h3>

		<ol class="commentlist">
		{foreach from=$object.Comment item="comment" name="fc_com"}
	
			<li class="comment even thread-{if $smarty.foreach.fc_com.iteration % 2 == 0}even{else}odd{/if}" id="li-comment-{$comment.id}">
			<a name="comment-{$comment.id}"></a>

			<div id="comment-{$comment.id}">
				<div class="comment-author vcard">
					<img alt='' src='http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=40' class='avatar avatar-40 photo avatar-default' height='40' width='40' />
					<cite class="fn">
					{if !empty($comment.url)}
						<a href='{$comment.url}' rel='external nofollow' target="_blank" class='url'>{$comment.author}</a>
					{else}
						{$comment.author}
					{/if}
					</cite> 
					<span class="says">says:</span>
				</div><!-- .comment-author .vcard -->
		
				<div class="comment-meta commentmetadata">{$comment.created|date_format:"%B %e, %Y"} at {$comment.created|date_format:"%l %p"}</a>
				</div><!-- .comment-meta .commentmetadata -->

				<div class="comment-body">
				<p>{$comment.description}</p>
				</div><!-- comment-body -->		
			</div><!-- #comment-##  -->
			
			</li>
		{/foreach}
		</ol>
	
	{/if}

	{$view->element('form_comments')}
	
{/if}

