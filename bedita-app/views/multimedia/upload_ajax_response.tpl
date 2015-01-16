{if !empty($errorMsg)}

    <div data-file-exists="true" class="bodybg">
        {if !empty($errorFileExist)}
            <table class="indexlist">
                <thead>
                    <tr>
                        <th colspan="2" style="padding: 5px 0px 5px 10px; margin-bottom:1px; font-weight:bold;">
                            <p>{t}Another multimedia object named{/t} "{$objectTitle|escape}" {t}already contains the file uploaded{/t}.
                            <p>{t}Choose one of the following operations{/t}</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width:15px; vertical-alig:middle; padding:5px 0px 5px 10px;">
                            <input type="button" value="{t}create new{/t}" class="uploadChoice" data-value="new_file_new_obj" />
                        </td>
                        <td>
                            {t}create new multimedia object containing the uploaded file{/t}
                        </td>
                    </tr>

                    {if empty($newObject) || !$newObject}
                    <tr>
                        <td style="width:15px; vertical-alig:middle; padding:5px 0px 5px 10px;">
                            <input type="button" value="{t}override{/t}" class="uploadChoice" data-value="new_file_old_obj" />
                        </td>
                        <td>
                            {t}replace old file with uploaded file in current multimedia object{/t}
                        </td>
                    </tr>
                    {/if}

                    <tr>
                        <td style="width:15px; vertical-alig:middle; padding:5px 0px 5px 10px;">
                            <input type="button" value='{t}Go to{/t}' name="goto" id="goto" data-href="{$html->url('/multimedia/view/')}{$objectId}"/>
                        </td>
                        <td>
                            {t}go to multimedia object "{$objectTitle|escape}"{/t}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="modalcommands">
                <input type="hidden" name="upload_other_obj_id" value="{$objectId}" />
                <input type="button" value="{t}Cancel{/t}" id="fileExistsCancel" />
            </div>
        {else}
            <h2 style="padding: 10px;">Error: {$errorMsg|escape}</h2>
        {/if}
    </div>

{elseif !empty($redirUrl)}
    <div data-redirect-url="{$html->url($redirUrl)}"></div>
{/if}