{$view->element('header')}

{$view->element('form_search')}


{if !empty($section)}
<hr/>
<h3>current section: $section</h3>
<a href="javascript:void(0)" class="open-close-link">open/close</a>
<div style="display: none">
<pre>
{dump var=$section}
</pre>
</div>
{/if}

<hr/>
<h3>publication: $publication</h3>
<a href="javascript:void(0)" class="open-close-link">open/close</a>
<div style="display: none">
<pre>
{dump var=$publication}
</pre>
</div>

<hr/>
<h3>configuration: $conf</h3>
<a href="javascript:void(0)" class="open-close-link">open/close</a>
<div style="display: none">
<pre>
{dump var=$conf}
</pre>
</div>

<hr/>
<h3>template variables available:</h3>
<a href="javascript:void(0)" class="open-close-link">open/close</a>
<div style="display: none">
<ol>
{foreach from=$view->_smarty->_tpl_vars key="tplK" item="tplV"}
<li> ${$tplK} - <em>{$tplV|get_type}</em> </li>
{/foreach}
</ol>
</div>

{$view->element('footer')}