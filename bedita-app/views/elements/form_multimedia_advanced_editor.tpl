{*
** detail of media item
*}
{if isset($object) && !empty($object.uri) && $object.ObjectType.name == "image"}

    {$html->script('jquery/jquery.tagImage', false)}

    <div class="tab"><h2>{t}Advanced editor{/t}</h2></div>

    <fieldset id="advanced-multimedia-editor" style="margin-left:-10px;">

        <div class="multimediaiteminside">

            <div class="toolbarToTag">
                <ul class="toolbarItems">
                    <li class="toolbarItem newAreas" action="drawAreas">definisci nuove aree</li>
                    <li class="toolbarItem newPoints" action="drawPoints">definisci nuovi tiranti</li>
                    <li class="toolbarItem modifyAll active" action="modify">modifica aree e tiranti</li>
                    <li class="toolbarItem viewAll" action="view">visualizza anteprima</li>
                </ul>
            </div>

            <div class="multimediaToTag" style="position: relative" id="image-{$object.id|default:''}">
                {$params = [
                        'width' => 600,
                        'longside' => false,
                        'mode' => 'fill',
                        'modeparam' => '000000',
                        'upscale' => false
                    ]
                }
                {$beEmbedMedia->object($object, $params)}
                <div class="toolbarMessage"></div>

                <div class="contextMenu" id="elementsMenu">
                    <ul class="toolbarMenu">
                        <li class="menuItem" action="none">posizione del testo<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchType" rel="tooltip">relativa all'area</li>
                                <li class="menuItem" action="switchType" rel="modal">relativa all'immagine</li>
                                <li class="menuItem" action="switchType" rel="postit">post-it</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">stile<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchStyle" rel="dotted">icona</li>
                                <li class="menuItem" action="switchStyle" rel="bordered">area con bordo</li>
                                <li class="menuItem" action="switchStyle" rel="filled">area piena</li>
                                <li class="menuItem" action="switchStyle" rel="hidden">nascosto</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">situazione iniziale<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchView" rel="true">visibile</li>
                                <li class="menuItem" action="switchView" rel="false">invisibile</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">modifica<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="modifyTitle">titolo</li>
                                <li class="menuItem" action="modifyDescription">descrizione</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="clone">duplica</li>
                        <li class="menuItem" action="remove">elimina</li>
                        <li class="menuItem break"></li>
                        <li class="menuItem" action="lockUnlock">blocca/sblocca</li>
                        <li class="menuItem" action="hide">nascondi</li>
                        <li class="menuItem" action="none">disponi<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="first">porta in primo piano</li>
                                <li class="menuItem" action="last">porta in ultimo piano</li>
                                <li class="menuItem" action="forward">porta avanti</li>
                                <li class="menuItem" action="backward">porta indietro</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="contextMenu" id="mediaMenu">
                    <ul class="toolbarMenu">
                        <li class="menuItem" action="create">nuova area</li>
                        <li class="menuItem" action="removeAll">elimina tutti</li>
                        <li class="menuItem break"></li>
                        <li class="menuItem" action="lockAll">blocca tutti</li>
                        <li class="menuItem" action="unlockAll">sblocca tutti</li>
                        <li class="menuItem" action="hideAll">nascondi tutti</li>
                        <li class="menuItem" action="showAll">visualizza tutti</li>
                    </ul>
                </div>

            </div>

        </div>


        <div style="margin: 20px 0 10px;"><h2>{t}Tag media{/t}</h2></div>

        <table class="htab" style="display:none;">
            <td rel="modifyContent">{t}modifica contenuto{/t}</td>
            <td rel="modifyCode">{t}modifica codice{/t}</td>
        </table>


        <div class="htabcontainer" id="manageTags">

            <div class="htabcontent" id="modifyContent" style="width: 96%; ">
                <textarea id="multimediaAdvanceTextArea" cols="" rows="" name="data[content]" style="height:{$height|default:200}px;" class="body"></textarea>
            </div>
            <script type="text/javascript">
            $(window).load(function() {
                $('#multimediaAdvanceTextArea').ckeditor(function(){},{
                    toolbar: [
                        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                        { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
                    ],
                    resize_enabled: true,
                    enterMode: CKEDITOR.ENTER_BR,
                    autoParagraph: false,
                    removePlugins: 'magicline'
                });
                $('#multimediaAdvanceTextArea').live('instanceReady.ckeditor', function(ev,editor){
                    editor.document.$.body.contenteditable = false;
                    startMultimediaAdvance(editor);
                })
                {literal} CKEDITOR.addCss('.imageTag{ padding-right:8px; padding-bottom:8px; } .imageTag div { background: none !important; padding-right:8px;  padding-bottom:8px; box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; -o-box-sizing: border-box; } .imageTag div:before { content: "Title:"; } .imageTag p{background: none !important;padding-right:8px;padding-bottom:8px; } .imageTag p:before{content: "Caption:"; } '); {/literal}

            })
            </script>

            <div class="htabcontent" id="modifyCode">
                <textarea cols="" rows="" name="data[abstract]" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; width: 100%; height:200px" class="abstract">{$object.abstract|default:''}</textarea>
                </table>
            </div>

            <div class="htabcontent" id="modifyCode">
                <textarea cols="" rows="" name="data[body]" style="display:none; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;   box-sizing: border-box; width: 100%; height:200px" class="json">{$object.body|replace:'\n':''|default:''}</textarea>
                </table>
            </div>

        </div>

    </fieldset>

{/if}