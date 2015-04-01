{* USED in Users module group detail *}

{foreach $objsRelated as $ob}
<tr>
    <td style="width: 50%">
        <a title="{$ob.BEObject.title|default:$ob.nickname}" href="{$html->url('/view/')}{$ob.id}">
            {$ob.title|default:$ob.nickname|truncate:38:'…':true}</a>
    </td>
    <td >
        <span class="listrecent {$conf->objectTypes[$ob.object_type_id].name}" style="vertical-align:middle; margin:0px 5px 0 0"></span>
        <a href="{$html->url('/view/')}{$ob.id}">{$conf->objectTypes[$ob.object_type_id].name}</a>
    </td>
    <td>
        <a href="{$html->url('/view/')}{$ob.id}">{$ob.status}</a>
    </td>
    <td>
        {$permissionset = []}
        {foreach $ob.Permission|default:[] as $obp}
            {$permissionset[] = $obp.flag}
        {/foreach}
        <select title="{t}add permission{/t}" multiple id="selectGroupPermission_{$ob.id}" name="data[Permission][{$ob.id}][]">
            {foreach from=$conf->objectPermissions item="permVal" key="permLabel"}
            <option 
            {if (in_array($permVal,$permissionset))}selected{/if} 
            value="{$permVal}">{t}{$permLabel}{/t}</option>
            {/foreach}
        </select>
    </td>
    <td style="text-align: right">
        <input class="BEbutton" name="remove" type="button" value="x">
    </td>
</tr>
{/foreach}