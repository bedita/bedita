
{if $object.object_type_id == $conf->objectTypes['video']['id']}
<div class="tab"><h2>{t}Captions{/t}</h2></div>
<div id="captions" data-start-idx="">

    <table class="indexlist">
		<thead>
			<tr>
				<th>{t}status{/t}</th>
				<th>{t}language{/t}</th>
				<th>IGHI</th>
			</tr>
		</thead>
        <tbody>
            {$i = 0}
            {foreach $object.captions as $lang => $caption}
            <tr>
                <td>
                    {foreach ['on', 'draft', 'off'] as $status}
                        <label>
                            <input type="radio" name="data[captions][{$i}][status]" value="{$status}" {if $caption.status == $status}checked{/if} />
                            {t}{$status}{/t}
                        </label>
                    {/foreach}
                </td>
                <td>
                    <input type="hidden" name="data[captions][{$i}][id]" value="{$caption.id|default:''}" />
                    <select name="data[captions][{$i}][lang]">
                        {foreach $conf->langOptions as $code => $name}
                            <option value="{$code}" {if $lang == $code}selected{/if}>{$name}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <textarea name="data[captions][{$i}][description]">{$caption.description|default:''}</textarea>
                </td>
            </tr>
            {$i = $i + 1}
            {/foreach}
        </tbody>
	</table>
</div>
{/if}
