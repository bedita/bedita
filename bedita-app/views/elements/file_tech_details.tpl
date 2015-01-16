{if (isset($object)) and (!empty($object.uri))}

    {if ($object.ObjectType.name == "image")}

        {if strpos($object.uri,'/') === 0}
            {assign_concat var="fileUrl"  1=$conf->mediaRoot  2=$object.uri}
        {else}
            {assign var="fileUrl"  value=$object.uri}
        {/if}
        {$imgInfo = $imageInfo->get($fileUrl)}
    {/if}

    <div class="tab"><h2>{t}Technical details{/t}</h2></div>

    <fieldset id="technicaldetails">

        <table class="bordered" style="border:1px solid #999; clear:both">

            <tr>
                <th>{t}filename{/t}:</th>
                <td colspan="3">{$object.name|default:""}</td>
            </tr>
            <tr>
                <th>{t}original filename{/t}:</th>
                <td colspan="3">{$object.original_name|escape|default:""}</td>
            </tr>
            <tr>
                <th>{t}mime type{/t}:</th>
                <td>{$object.mime_type|default:""}</td>
                <th>{t}filesize{/t}:</th>
                <td>{$object.file_size|filesize}</td>
            </tr>

        {if strtolower($object.ObjectType.name) == "application"}
            
            <tr>
                <th>{t}Width{/t}:</th>
                <td><input type="text" size="6" name="data[width]" value="{$object.width}"/></td>
                <th>{t}Height{/t}:</th>
                <td><input type="text" size="6" name="data[height]" value="{$object.height}"/></td>
            </tr>
            <tr>
                <th>{t}Version{/t}:</th>
                <td colspan="3"><input type="text" name="data[application_version]" value="{$object.application_version}"/></td>
            </tr>
            <tr>
                <th>{t}Text direction{/t}:</th>
                <td colspan="3">
                    <select name="data[text_dir]">
                        <option value=""></option>
                        <option value="ltr" {if $object.text_dir == 'ltr'}selected="selected"{/if}>{t}left to right{/t}</option>
                        <option value="rtl" {if $object.text_dir == 'rtl'}selected="selected"{/if}>{t}right to left{/t}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>{t}Text lang{/t}:</th>
                <td colspan="3"><input type="text" name="data[text_lang]" value="{$object.text_lang}"/></td>
            </tr>

        {/if}


        {if $object.ObjectType.name == "image" && $object.mime_type != 'image/svg+xml'}
            
            <tr>
                <th nowrap>{t}Human readable type{/t}:</th>
                <td>{$imgInfo.hrtype}</td>
                <th>{t}Orientation{/t}:</th>
                <td>{$imgInfo.orientation}</td>
            </tr>
            <tr>
                <th>{t}Width{/t}:</th>
                <td>{$imgInfo.w}</td>
                <th>{t}Height{/t}:</th>
                <td>{$imgInfo.h}</td>
            </tr>
            <tr>
                <th>{t}Bit depth{/t}:</th><td>{$imgInfo.bits}</td>
                <th>{t}Channels{/t}:</th><td>{$imgInfo.channels}</td>
            </tr>

        {/if}
            
            <tr>
                <th>{t}Url{/t}: <!-- <input type="button" onclick="$('#mediaurl').copy();" value="{t}copy{/t}" /> --> </th>
                <td colspan="3">

                {if (substr($object.uri,0,7) == 'http://') or (substr($object.uri,0,8) == 'https://')}
                    {assign var="uri" value=$object.uri}
                {else}
                    {assign_concat var="uri" 1=$conf->mediaUrl 2=$object.uri}
                {/if}
                    <a target="_blank" id="mediaurl" href="{$uri}">
                        {$uri}
                    </a>
                </td>
            </tr>
            {if !empty($html->params.isAjax)}
            <tr>
                <th>{t}id{/t}:</th>
                <td>
                    {$object.id}
                </td>
                <th>{t}Unique name{/t}:</th>
                <td>
                    {$object.nickname}
                </td>
            </tr>
            {/if}

            {if ($object.ObjectType.name != "image")}
            <tr>
                <th>{t}thumbnail{/t}</th>
                <td colspan="3">
                <input type="text" name="data[thumbnail]" value="{$object.thumbnail|default:''}" style="width: 350px;"/>
                {*if !empty($object.thumbnail)}
                    <img src="{$object.thumbnail}" alt=""/>
                {/if*}
                </td>
            </tr>
            {/if}
        </table>

    </fieldset>

{/if}