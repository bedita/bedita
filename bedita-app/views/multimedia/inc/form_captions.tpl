
{if $object.object_type_id == $conf->objectTypes['video']['id']}
<div class="tab"><h2>{t}Captions{/t}</h2></div>
<div id="captions">

    <table class="indexlist">
		<thead>
			<tr>
				<th>{t}status{/t}</th>
				<th>{t}language{/t}</th>
				<th>{t}contents{/t}</th>
			</tr>
		</thead>
        <tbody>
            {foreach $object.captions as $caption}
            <tr>
                <td>
                    <input type="hidden" name="data[captions][{$caption@index}][id]" value="{$caption.id|default:''}" />
                    {foreach ['on', 'draft', 'off'] as $status}
                        <label>
                            <input type="radio" name="data[captions][{$caption@index}][status]" value="{$status}" {if $status == $caption.status}checked{/if} />
                            {t}{$status}{/t}
                        </label>
                    {/foreach}
                </td>
                <td>
                    <select name="data[captions][{$caption@index}][lang]">
                        {foreach $conf->langOptions as $code => $name}
                            <option value="{$code}" {if $code == $caption.lang}selected{/if}>{$name}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <textarea name="data[captions][{$caption@index}][description]">{$caption.description|default:''}</textarea>
                </td>
            </tr>
            {/foreach}

            <tr>
                <td>
                    {foreach ['on', 'draft', 'off'] as $status}
                        <label>
                            <input type="radio" name="data[captions][{$caption@total}][status]" value="{$status}" {if $status == $object.status|default:$conf->defaultStatus}checked{/if} />
                            {t}{$status}{/t}
                        </label>
                    {/foreach}
                </td>
                <td>
                    <select name="data[captions][{$caption@total}][lang]">
                        {foreach $conf->langOptions as $code => $name}
                            <option value="{$code}" {if $code == $conf->defaultLang}selected{/if}>{$name}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <textarea name="data[captions][{$caption@total}][description]"></textarea>
                </td>
            </tr>
        </tbody>
	</table>
</div>
{/if}
