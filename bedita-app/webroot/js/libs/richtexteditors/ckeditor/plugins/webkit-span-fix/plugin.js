/**
 * Plugin to fix this issue in webkit browsers
 * Bug description http://dev.ckeditor.com/ticket/9998

 * It removes spans created by webkit browsers
 * It preserves existing spans created by the user
 * Logs information on the console when removing, merging etc.
 *
 * @author pr0nbaer
 * @version 0.0.2
 */
(function() {
    // Plugin registrieren
    CKEDITOR.plugins.add('webkit-span-fix', {
        // Plugin initialisiert
        init: function(editor) {

            ////////////////////////////////////////////////////////////////////////
            // Webkit Span Bugfix //////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////

            // Nur für Webkit Browser
            if (CKEDITOR.env.webkit) {

                console.log('>>> Using Webkit Span Bugfix');

                var getParentsToClosestBlockElement = function(node) {
                    var parentsToBlockElement = [];
                    var parents;

                    if (node instanceof CKEDITOR.dom.element || node instanceof CKEDITOR.dom.text) {
                        // Alle Elternknoten des Knotens holen (inkl. des Knotens selbst)
                        parents = node.getParents(true);

                        // Wenn Elternknoten vorhanden
                        if (parents !== null) {

                            // Elternelementse durchschleifen
                            for (var i = 0; i < parents.length; i++) {

                                parentsToBlockElement[i] = parents[i];

                                // Wenn Elternelement ein Blockelement, dann das vorherige
                                // Elternelement wegspeichern und abbrechen
                                if (i >= 1 && parents[i] instanceof CKEDITOR.dom.element && parents[i].getComputedStyle('display') == 'block') {
                                    break;
                                }
                            }
                        }
                    }
                    return parentsToBlockElement;
                };

                var getNextNodeSiblingsOfSelection = function() {
                    // Rückgabearray
                    var siblings  = [];
                    // Selektion holen
                    var selection = editor.getSelection();
                    var nextNode;
                    var ranges;
                    var nextNodeParents;
                    var element;

                    // Wenn Selektion vorhanden
                    if (selection !== null) {

                        // Ranges der Selektion holen
                        ranges = selection.getRanges();

                        // Wenn Ranges vorhanden
                        if (ranges.length) {

                            nextNode = ranges[0].getNextNode();

                            // Wenn Knoten vorhanden
                            if (nextNode !== null) {

                                nextNodeParents = getParentsToClosestBlockElement(nextNode);

                                // Wenn Element vorhanden
                                if (nextNodeParents[nextNodeParents.length - 2] !== undefined) {

                                    element = nextNodeParents[nextNodeParents.length - 2];

                                    // Das Element und alle seine nachfolgenden Elemente (in der gleichen Ebene)
                                    // wegspeichern
                                    do {

                                        siblings.push(element);
                                        element = element.getNext();

                                    } while (element !== null);

                                }

                            }

                        }

                    }

                    var redoSelection = function() {
                        if (selection !== null && ranges !== null && ranges.length) {
                            selection.selectRanges(ranges);
                        }
                    };

                    return {
                        'siblings': siblings,
                        'redoSelection': redoSelection,
                        'nextNode': nextNode
                    };

                };

                // Wenn Editor im Editierungsmodus ist (WYSIWYG Modus)
                editor.on('contentDom', function() {

                    // Wenn KeyDown Event getriggert wurde
                    editor.document.on('keydown', function(event) {

                        var nextNodeSiblingsOnKeyDown = getNextNodeSiblingsOfSelection();

                        // Einmalig beim keyDown Event das KeyUp Event binden
                        // => Wird dann aufgerufen, nachdem Chrome die SPANs gesetzt hat! ;)
                        editor.document.once('keyup', function(event) {

                            var nextNodeSiblingsOnKeyUp = getNextNodeSiblingsOfSelection();

                            var blockElementsMerged = false;

                            if (nextNodeSiblingsOnKeyDown.nextNode !== null && nextNodeSiblingsOnKeyUp.nextNode !== null) {

                                var nextNodeOnKeyDownParents = getParentsToClosestBlockElement(nextNodeSiblingsOnKeyDown.nextNode);
                                var nextNodeOnKeyUpParents = getParentsToClosestBlockElement(nextNodeSiblingsOnKeyUp.nextNode);

                                if (nextNodeOnKeyDownParents[nextNodeOnKeyDownParents.length - 1].getAddress().join('|') != nextNodeOnKeyUpParents[nextNodeOnKeyUpParents.length - 1].getAddress().join('|')) {

                                    blockElementsMerged = true;

                                }

                            }

                            if (blockElementsMerged) {

                                console.log('>>> Detected merge of block elements');

                                for (var i = 0; i < nextNodeSiblingsOnKeyDown.siblings.length; i++) {

                                    if (nextNodeSiblingsOnKeyUp.siblings[i] === undefined) break;

                                    nodeBeforeKey = nextNodeSiblingsOnKeyDown.siblings[i];
                                    nodeAfterKey = nextNodeSiblingsOnKeyUp.siblings[i];

                                    // Textknoten wurde in einen Span umgewandelt
                                    if (nodeBeforeKey instanceof CKEDITOR.dom.text && nodeAfterKey instanceof CKEDITOR.dom.element && nodeAfterKey.getName() == 'span') {

                                        console.log('>>> Remove Webkit Span', nodeAfterKey.getOuterHtml());
                                        nodeAfterKey.remove(true);

                                    // In einem Inline-Element wurde das Style-Attribut geändert
                                    } else if (nodeBeforeKey instanceof CKEDITOR.dom.element
                                            && nodeAfterKey instanceof CKEDITOR.dom.element
                                            && nodeAfterKey.getComputedStyle('display').match(/^inline/)
                                            && nodeAfterKey instanceof CKEDITOR.dom.element
                                            && nodeAfterKey.getName() == nodeBeforeKey.getName()
                                            && nodeAfterKey.getAttribute('style') != nodeBeforeKey.getAttribute('style')) {

                                        if ( nodeBeforeKey.getAttribute('style') != null ) {

                                            console.log('>>> Update Webkit Span Style Attribute', nodeAfterKey.getOuterHtml(), 'to', nodeBeforeKey.getAttribute('style'));
                                            nodeAfterKey.setAttribute('style', nodeBeforeKey.getAttribute('style'));

                                        } else {

                                            console.log('>>> Remove Webkit Span Style Attribute', nodeAfterKey.getOuterHtml());
                                            nodeAfterKey.removeAttribute('style');

                                        }

                                    }
                                    // Bugfix => Selektion wiederherstellen
                                    nextNodeSiblingsOnKeyUp.redoSelection();
                                }
                            }
                        });
                    });
                });
            }
        }
    });
})();