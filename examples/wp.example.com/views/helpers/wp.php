<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Base annotation
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (Wed, 25 Nov 2009) $
 *
 * $Id: annotation.php 2487 2009-11-25 16:56:37Z ste $
 */

/* 
 * WpHelper class
 *
 * sample helper for frontend view utility
 */

class WpHelper extends AppHelper {

	var $helpers = array("BeTime");

	public function showComments($comments) {
		$html = "<ol class=\"commentlist\">";
		if (!empty($comments)) {
			$html .= $this->threadItem($comments);
		}
		$html .= "</ol>";
		return $html;
	}

	private function threadItem($comments) {
		$html = "";
		foreach ($comments as $k => $comment) {
			$liclass = ($k % 2 == 0)? 'even' : 'odd';
			$html .= '<li class="comment ' . $liclass . ' thread-' . $liclass .
				'" id="li-comment-' . $comment["id"] . '"><a name="comment-' . $comment["id"] .'"></a>' .
				'<div id="comment-' . $comment["id"] . '">' .
				'<div class="comment-author vcard">' .
				'<img alt="" src="http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=40" class="avatar avatar-40 photo avatar-default" height="40" width="40" />' .
				'<cite class="fn">';
			if (!empty($comment["url"])) {
				$html .= '<a href="' . $comment["url"] . '" rel="external nofollow" target="_blank" class="url">' . $comment["author"] . '</a>';
			} else {
				$html .= $comment["author"];
			}
			$html .= '</cite>' .
				' <span class="says">says:</span>' .
				'</div>' .
				'<div class="comment-meta commentmetadata">' . $this->BeTime->date($comment["created"],"%B %e, %Y") . ' at ' . $this->BeTime->date($comment["created"],"%l %p") . '</a>' .
				'</div>' .
				'<div class="comment-body"><p>' . $comment["description"] . '</p></div>' .
				'<div class="reply" rel="' . $comment["id"] . '"><a rel="nofollow" class="comment-reply-link" href="javascript: void(0);">Reply</a></div>' .
				'</div>';
			if (!empty($comment["children"])) {
				$html .= '<ul class="children">';
				$html .= $this->threadItem($comment["children"]);
				$html .= '</ul>';
			}
			$html .= '</li>';
		}
		return $html;
	}



//	{foreach from=$object.Comment item="comment" name="fc_com"}
//
//			<li class="comment even thread-{if $smarty.foreach.fc_com.iteration % 2 == 0}even{else}odd{/if}" id="li-comment-{$comment.id}">
//			<a name="comment-{$comment.id}"></a>
//
//			<div id="comment-{$comment.id}">
//				<div class="comment-author vcard">
//					<img alt='' src='http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=40' class='avatar avatar-40 photo avatar-default' height='40' width='40' />
//					<cite class="fn">
//					{if !empty($comment.url)}
//						<a href='{$comment.url}' rel='external nofollow' target="_blank" class='url'>{$comment.author}</a>
//					{else}
//						{$comment.author}
//					{/if}
//					</cite>
//					<span class="says">says:</span>
//				</div><!-- .comment-author .vcard -->
//
//				<div class="comment-meta commentmetadata">{$comment.created|date_format:"%B %e, %Y"} at {$comment.created|date_format:"%l %p"}</a>
//				</div><!-- .comment-meta .commentmetadata -->
//
//				<div class="comment-body">
//				<p>{$comment.description}</p>
//				</div><!-- comment-body -->
//
//				<div class="reply" rel="{$comment.id}"><a rel='nofollow' class='comment-reply-link' href='javascript: void(0);'>Reply</a></div>
//				<!-- .reply -->
//
//			</div><!-- #comment-##  -->
//
//			</li>
//		{/foreach}
}

?>
