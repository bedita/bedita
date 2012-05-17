{$view->element('header')}

{$view->element('form_search')}


{if !empty($section)}
<hr/>
<h3>{t}current section{/t}: $section</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$section}
</pre>
</div>
{/if}

<hr/>
<h3>{t}publication{/t}: $publication</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$publication}
</pre>
</div>

<hr/>
{if empty($BEAuthUser)}
<h3>{t}user not logged{/t}: <a href="{$html->url('/')}login">{t}login{/t}</a> </h3>
{else}
<h3>{t}user logged{/t}: $BEAuthUser</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$BEAuthUser}
<a href="{$html->url('/')}logout">{t}logout{/t}</a> 
</pre>
</div>
{/if}

<hr/>
<h3>{t}session data{/t}: $session-&gt;read()</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$session->read()}
</pre>
</div>

<hr/>
<h3>{t}configuration{/t}: $conf</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$conf}
</pre>
</div>

<hr/>
<h3>{t}template variables available{/t}:</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<ol>
{foreach from=$view->viewVars key="tplK" item="tplV"}
<li> ${$tplK} - <em>{$tplV|get_type}</em> </li>
{/foreach}
</ol>
</div>

<hr/>
<h3>{t}thumbnails test{/t}:</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">

{if !empty($section.childContents)}
{foreach from=$section.childContents item="object"}
<ul>
	{if $object.object_type == 'Image' }
	<li>
	crop: {$beEmbedMedia->object($object, ['width'=>200, 'height'=>200, 'mode'=>'crop'])}
	</li>
	<li>
	croponly: {$beEmbedMedia->object($object, ['width'=>300, 'height'=>300, 'mode'=>'croponly', 'modeparam'=>'BL' ] )}
	</li>
	<li>
	croponly: {$beEmbedMedia->object($object, ['width'=>300, 'height'=>300, 'mode'=>'croponly', 'modeparam'=>'TR'] )}
	</li>
	<li>
	resize stretch: {$beEmbedMedia->object($object, ['width'=>100, 'height'=>300, 'mode'=>'resize', 'modeparam'=>'stretch'] )}
	</li>
	<li>
	resize fill: {$beEmbedMedia->object($object, ['width'=>400, 'height'=>100, 'mode'=>'resize', 'modeparam'=>'fill', 'bgcolor'=>'CCCCCC' ] )}
	</li>
	{/if}
</ul>
{/foreach}
{/if}
</div>

<hr/>
{$view->element('footer')}