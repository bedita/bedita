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
{foreach from=$view->_smarty->_tpl_vars key="tplK" item="tplV"}
<li> ${$tplK} - <em>{$tplV|get_type}</em> </li>
{/foreach}
</ol>
</div>

{$view->element('footer')}