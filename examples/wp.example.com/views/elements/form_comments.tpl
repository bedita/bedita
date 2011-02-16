
<a name="comment" id="anchor-comment"></a>
{assign var="userLogged" value=$session->read($conf->session.sessionUserKey)}


<div id="respond">
	<h3 id="reply-title">Leave a Reply <span id="cancel-reply"><a href="javascript:void(0);">Cancel reply</a></span></h3>
	<form action="{$html->url('/saveComment')}" method="post">
	<input type="hidden" name="data[object_id]" value="{$section.currentContent.id}" />
	<input type="hidden" id="thread_parent_id" name="thread_parent_id" value="" />

	{if empty($userLogged)}

		<p class="comment-notes">Your email address will not be published. Required fields are marked <span class="required">*</span></p>
		<p class="comment-form-author"><label for="author">Name</label> <span class="required">*</span><input id="author" name="data[author]" type="text" value="" size="30" aria-required='true' /></p>

		<p class="comment-form-email"><label for="email">Email</label> <span class="required">*</span><input id="email" name="data[email]" type="text" value="" size="30" aria-required='true' /></p>

		<p class="comment-form-url"><label for="url">Website</label><input id="url" name="data[url]" type="text" value="" size="30" /></p>
	{/if}

	<p class="comment-form-comment"><label for="comment">Comment</label><textarea id="comment" name="data[description]" cols="45" rows="8" aria-required="true"></textarea></p>

	{if empty($userLogged)}
		<p>
		<img src="{$html->url('/captchaImage')}" style="margin-right: 8px;" /><br/>
		<label>{t}Write the text you see in the image above{/t}</label><br/>
		<input type="text" name="captcha" id="captcha" style="width: 240px; margin: 6px 0;"  />
		</p>
	{/if}

	<p class="form-submit">
		<input name="submit" type="submit" id="submit" value="Post Comment" />
	</p>
	</form>

{$session->flash('error')}

</div><!-- #respond -->

