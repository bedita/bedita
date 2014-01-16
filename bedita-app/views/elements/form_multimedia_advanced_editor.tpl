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
                    <li class="toolbarItem newAreas" action="drawAreas">{t}define interactive areas{/t}</li>
                    <li class="toolbarItem newPoints" action="drawPoints">{t}define interactive points{/t}</li>
                    <li class="toolbarItem modifyAll active" action="modify">{t}edit interactive elements{/t}</li>
                    <li class="toolbarItem viewAll" action="view">{t}preview interactive image{/t}</li>
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
                        <li class="menuItem" action="none">{t}interactive pop-up positon{/t}<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchType" rel="tooltip">{t}relative to area{/t}</li>
                                <li class="menuItem" action="switchType" rel="modal">{t}relative to image{/t}</li>
                                <li class="menuItem" action="switchType" rel="postit">{t}post-it{/t}</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">{t}style{/t}<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchStyle" rel="dotted">{t}icon{/t}</li>
                                <li class="menuItem" action="switchStyle" rel="bordered">{t}bordered area{/t}</li>
                                <li class="menuItem" action="switchStyle" rel="filled">{t}filled area{/t}</li>
                                <li class="menuItem" action="switchStyle" rel="hidden">{t}hidden area{/t}</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">{t}initial situation{/t}<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="switchView" rel="true">{t}visible{/t}</li>
                                <li class="menuItem" action="switchView" rel="false">{t}hidden{/t}</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="none">{t}Modify{/t}<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="modifyTitle">{t}title{/t}</li>
                                <li class="menuItem" action="modifyDescription">{t}description{/t}</li>
                            </ul>
                        </li>
                        <li class="menuItem" action="clone">{t}Clone{/t}</li>
                        <li class="menuItem" action="remove">{t}Delete{/t}</li>
                        <li class="menuItem break"></li>
                        <li class="menuItem" action="lockUnlock">{t}lock{/t}/{t}unlock{/t}</li>
                        <li class="menuItem" action="hide">{t}hide{/t}</li>
                        <li class="menuItem" action="none">{t}arrange layers{/t}<span class="subMenuIcon">></span>
                            <ul class="toolbarSubMenu">
                                <li class="menuItem" action="first">{t}bring to front{/t}</li>
                                <li class="menuItem" action="last">{t}send to back{/t}</li>
                                <li class="menuItem" action="forward">{t}bring forward{/t}</li>
                                <li class="menuItem" action="backward">{t}send backward{/t}</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="contextMenu" id="mediaMenu">
                    <ul class="toolbarMenu">
                        <li class="menuItem" action="create">{t}new interactive area{/t}</li>
                        <li class="menuItem" action="removeAll">{t}delete all{/t}</li>
                        <li class="menuItem break"></li>
                        <li class="menuItem" action="lockAll">{t}lock all{/t}</li>
                        <li class="menuItem" action="unlockAll">{t}unlock all{/t}</li>
                        <li class="menuItem" action="hideAll">{t}hide all{/t}</li>
                        <li class="menuItem" action="showAll">{t}Show all{/t}</li>
                    </ul>
                </div>

            </div>

        </div>


        <div style="margin: 20px 0 10px;"><h2>{t}contents{/t}</h2></div>

        <table class="htab" style="display:none;">
            <td rel="modifyContent"></td>
            <td rel="modifyCode"></td>
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