
<a name="comment"></a>
{assign var="userLogged" value=$session->read($conf->session.sessionUserKey)}
{if empty($userLogged)}	

	<div id="respond">

		<h3 id="reply-title">Leave a Reply</h3>
		<form action="{$html->url('/saveComment')}" method="post">
		<input type="hidden" name="data[object_id]" value="{$section.currentContent.id}" />
		<p class="comment-notes">Your email address will not be published. Required fields are marked <span class="required">*</span></p>
		<p class="comment-form-author"><label for="author">Name</label> <span class="required">*</span><input id="author" name="data[author]" type="text" value="" size="30" aria-required='true' /></p>

		<p class="comment-form-email"><label for="email">Email</label> <span class="required">*</span><input id="email" name="data[email]" type="text" value="" size="30" aria-required='true' /></p>

		<p class="comment-form-url"><label for="url">Website</label><input id="url" name="data[url]" type="text" value="" size="30" /></p>
												
		<p class="comment-form-comment"><label for="comment">Comment</label><textarea id="comment" name="data[description]" cols="45" rows="8" aria-required="true"></textarea></p>

		<p>
		<img src="{$html->url('/captchaImage')}" style="margin-right: 8px;" /><br/>
		<label>{t}Write the text you see in the image above{/t}</label><br/>
		<input type="text" name="captcha" id="captcha" style="width: 240px; margin: 6px 0;"  /> 
		</p>

		<p class="form-submit">
			<input name="submit" type="submit" id="submit" value="Post Comment" />
			<input type='hidden' name='comment_post_ID' value='35' id='comment_post_ID' />
			<input type='hidden' name='comment_parent' id='comment_parent' value='0' />
		</p>
		</form>

		{if $session->flash('error')}{/if}

	</div><!-- #respond -->

{/if}

