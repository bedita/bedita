/**
multimedia custom js
*/

/**
multimedia custom js
*/

var texts = {}

function startMultimediaAdvance(e) {
    e.document.on('keyup', changedHtml);
    $('.cke_button').live('mousedown', changedHtml);
    /* Definizione delle modalità e interazione con toolbar */
        
    var mode =  $('.toolbarItem.active').attr('action');
    $('.multimediaToTag').addClass(mode);
    var isDrawing = false; //alla modalità draw si aggiunge isDrawing al mousedown
    var modes = ['drawAreas', 'drawPoints', 'modify', 'view'];
    var areaTypes = ['tooltip', 'modal', 'postit'];
    var areaStyles = ['dotted', 'bordered', 'filled', 'hidden'];
    var areaClasses = ['xsmall', 'small', 'medium', 'large','xlarge'];
    var nodesNames = ['n','ne','e','se','s','sw','w','nw','c'];
    var pointSizes = ['xsmall','small','medium','large','xlarge'];
    var pointSizesInitials = ['XS','S','M','L','XL'];

    function changeMode(action){
        if(mode=='view') $('.multimediaToTag').tagImage('destroy');
        tempModes = modes.slice();
        for(var i = tempModes.length-1; i>=0; i--){
            if(tempModes[i] == action){
                tempModes.splice(i,1);
            }
        }
        mode = action;
        $('.toolbarItem').removeClass('active');
        $('.toolbarItem[action="'+action+'"]').addClass('active');
        $('.multimediaToTag').removeClass(tempModes.join(' ')).addClass(action);
        if (mode == 'view') setPreview();
    }

    $('.toolbarItem').bind('click', function(){
        changeMode($(this).attr('action'));
    });
    
    /*Variabili di base */
    
    var tags = {}; // informazioni sulla taggatura dell'immagine; va a finire in abstract
    tags.areas = {}; // stile e id delle aree di tag
    tags.points = {}; // stile e id delle captions
    texts = {}; // titolo e testo collegato
    var $_mediaToTag = $('.multimediaToTag img'); //immagine da taggare
    var mediaId = $("input[name='data[id]']").val(); //id dell'elemento multimediale
    var areaPrefix = 'imageTag-'+mediaId+'-'; //utilizzata nell'editor del testo, verrà salvata
    var areaPrefixHash = '#'+areaPrefix; //utilizzata nell'editor del testo, verrà salvata
    var pointPrefix = 'imagePoint-'+mediaId+'-'; //utilizzata nell'editor del testo, verrà salvata
    var pointPrefixHash = '#'+pointPrefix; //utilizzata nell'editor del testo, verrà salvata
    var editorSuffix = '-editor'; // utilizzata sulle aree dell'immagine per distinguere id da tinyMce
    var mediaWidth = parseInt($_mediaToTag.width());
    var mediaHeight = parseInt($_mediaToTag.height());
    var mediaRatio = mediaWidth/mediaHeight;
    $_mediaToTag.parent().css({
        width: mediaWidth,
        height: mediaHeight
    })
    //in percentuale; dimensione minima e massima dell'area sull'immagine
    var areaMinWidth = 3; 
    var areaMinHeight = 3;
    var areaMaxWidth = 100;
    var areaMaxHeight = 100;

    var startXPx, startYPx, startX, startY, endXPx, endYPx, endX, endY, width, height, pointWidth, pointHeight, pointWidthPx, pointHeightPx;
    var areasId = 0;
    var pointsId = 0;
    var zIndex = 1;
    
    /*Definizione funzioni per drawing, dragging, resizing */

    function startDrawingArea(e){
        e.preventDefault();
        isDrawing = true;
        $('.multimediaToTag').addClass('isDrawing');
        
        local = globalToLocal(e);
        startX = local.x;
        startY = local.y;
        
        createArea(areasId,'left:'+startX+'%;top:'+startY+'%;z-index:' + zIndex);
        var $_div = $(areaPrefixHash+areasId+editorSuffix);

        var metrics = {};
        $(document).bind({
            mousemove: function(e){
                //e.preventDefault();
                local = globalToLocal(e);
                endX = local.x;
                endY = local.y;
                width = Math.abs(endX - startX);
                if(e.shiftKey){
                    height = width*mediaRatio;
                } else {
                    height = Math.abs(endY - startY);
                }
                
                //Bounding box per drawing
                //Diverso da setAreaMetrics perché permetto disegni in negativo e molto piccoli
                if(startX>endX){
                    leftPos = startX - width;
                } else {
                    leftPos = parseFloat($_div.css('left'));
                }
                if(startY>endY){
                    topPos = startY - height;
                } else {
                    topPos = parseFloat($_div.css('top'));  
                }
                if(leftPos<0){
                    leftPos = 0;
                    width = startX;
                } else if(leftPos + width>100){
                    width = 100 -  leftPos;
                }
                if(topPos<0){
                    topPos = 0;
                    height = startY;
                } else if(topPos + height>100){
                    height = 100 - topPos;
                }

                $_div.css({
                    left: leftPos + '%',
                    top: topPos + '%',
                    width: width + '%',
                    height: height + '%',
                    zIndex: zIndex
                });

                metrics = {
                    left: leftPos,
                    top: topPos,
                    height: height,
                    width: width,
                    zIndex: zIndex
                };
            },
            mouseup: function(e){
                isDrawing = false;
                $('.multimediaToTag').removeClass('isDrawing');
                metrics = setAreaMetrics(metrics, null, null, true);
                $_div.css({
                    left: metrics.left + '%',
                    top: metrics.top + '%',
                    width: metrics.width + '%',
                    height: metrics.height + '%',
                    zIndex: metrics.zIndex
                });

                var actualId = areasId;
                message({
                    message: 'titolo di questa area:', 
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setArea(actualId);
                        setAreaTitle(actualId, title, true);
                        requireAreaDescription(actualId);
                        areasId++;
                    },
                    onCancel: function(){
                        $(areaPrefixHash+actualId+editorSuffix).remove();
                    }
                });

                $(document).unbind('mousemove');
                $(document).unbind('mouseup');
            }
        })
    };

    function startDraggingArea(e){
        e.preventDefault();
        local = globalToLocal(e);
        startX = local.x;
        startY = local.y;

        var $_div = $(e.currentTarget);
        if(!$_div.hasClass('locked')){
            var id = $_div.attr('id').split('-')[2];
            
            $(document).bind({
                mousemove: function(e){
                    //e.preventDefault();
                    local = globalToLocal(e);
                    endX = local.x;
                    endY = local.y;
                    deltaX = endX - startX;
                    deltaY = endY - startY;
                    var metrics = {
                        left: parseFloat($_div.css('left')) + deltaX,
                        top: parseFloat($_div.css('top')) + deltaY,
                        width: parseFloat($_div.css('width')),
                        height: parseFloat($_div.css('height')),
                        zIndex: parseInt($_div.css('z-index'))
                    };
                    metrics = setAreaMetrics(metrics,id,true);
                    $_div.css({
                        left: metrics.left + '%',
                        top: metrics.top + '%'
                    });
                    startX = endX;
                    startY = endY;
                },
                mouseup: function(e){
                    $(document).unbind('mousemove');
                    $(document).unbind('mouseup');
                }
            })
        };
    };

    function startResizingAreas(e){
        e.preventDefault();
        local = globalToLocal(e);
        startX = local.x;
        startY = local.y;
        
        var $_div = $(e.target).parent();
        if(!$_div.hasClass('locked')){
            var id = $_div.attr('id').split('-')[2];
            var target = e.target.className.split(' ')[1]; //Angolo da cui stiamo ridimensionando
            
            $(document).bind({
                mousemove: function(e){
                    local = globalToLocal(e);
                    endX = local.x;
                    endY = local.y;
                    
                    if(endX<0){
                        endX = 0;
                    } else if (endX>100){
                        endX = 100;
                    }
                    if(endY<0){
                        endY = 0;
                    } else if (endY>100){
                        endY = 100;
                    }
                    deltaX = endX - startX;
                    deltaY = endY - startY;

                    
                    var metrics = {};
                    metrics.left = parseFloat($_div.css('left'));
                    metrics.top = parseFloat($_div.css('top'));
                    width = parseFloat($_div.css('width'));
                    height = parseFloat($_div.css('height'));
                    switch(target){
                        case 'angleNE':
                            metrics.width = width + deltaX;
                            metrics.height = height - deltaY;
                            metrics.top = endY;
                            break;
                        case 'angleSE':
                            metrics.width = width + deltaX;
                            metrics.height = height + deltaY;
                            break;
                        case 'angleSW':
                            metrics.width = width - deltaX;
                            metrics.height = height + deltaY;
                            metrics.left = endX;
                            break;
                        case 'angleNW':
                            metrics.width = width - deltaX;
                            metrics.height = height - deltaY;
                            metrics.left = endX;
                            metrics.top = endY;
                            break;
                    }
                    metrics.zIndex = parseInt($_div.css('z-index'));
                    metrics = setAreaMetrics(metrics,id,true);
                    $_div.css({
                        left: metrics.left + '%',
                        top: metrics.top + '%',
                        width: metrics.width + '%',
                        height: metrics.height + '%',
                        zIndex: metrics.zIndex
                    });

                    startX = endX;
                    startY = endY;
                },
                mouseup: function(e){
                    $(document).unbind('mousemove');
                    $(document).unbind('mouseup');
                }
            })
        }
    }

    function startDrawingPoint(e){
        e.preventDefault();
        isDrawing = true;
        $('.multimediaToTag').addClass('isDrawing');
        
        local = globalToLocal(e);
        startX = local.x;
        startY = local.y;
        startXPx = local.xPx;
        startYPx = local.yPx;
        
        createPoint(pointsId,{'hotspotX':startX,'hotspotY':startY},'z-index:' + zIndex);
        var $_div = $(pointPrefixHash+pointsId+editorSuffix);
        var $_point = $_div.find('.point');
        var pointWidthPx = $_point.width();
        var pointHeightPx = $_point.height();
        var pointWidth = pointWidthPx * 100 / mediaWidth;
        var pointHeight =  pointHeightPx * 100 / mediaHeight;

        var metrics = {};
        $(document).bind({
            mousemove: function(e){
                //e.preventDefault();
                local = globalToLocal(e);
                endX = local.x;
                endY = local.y;

                if(endX + pointWidth/2 > 100){
                    endX = 100 - pointWidth/2;
                } else if (endX - pointWidth/2<0){
                    endX = pointWidth/2;
                }
                if(endY + pointHeight/2 > 100){
                    endY = 100 - pointHeight/2;
                } else if (endY - pointHeight/2<0){
                    endY = pointHeight/2;
                }
                
                $_point.css({ left: endX+"%", top: endY+"%"});

                endXPx = endX * mediaWidth / 100;
                endYPx = endY * mediaHeight / 100;

                var pointXPx = $_point.position().left + parseInt($_point.css('margin-left'));
                var pointYPx = $_point.position().top + parseInt($_point.css('margin-top'));
                var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);

                if($_point.hasClass('connectionLocked')) {
                    endXPx = pointNodes[$('#point').data('connectionNodeIndex')][0];
                    endYPx = pointNodes[$('#point').data('connectionNodeIndex')][1];
                } else {
                    var closestEndPoint = findClosestNode(pointNodes,startXPx,startYPx)
                    endXPx = pointNodes[closestEndPoint][0];
                    endYPx = pointNodes[closestEndPoint][1];
                    $_div.data('connectionNodeIndex',closestEndPoint);
                }
                $_div.find('.pointLineConnection').css({ left: endXPx * 100 / mediaWidth + '%', top: endYPx * 100 / mediaHeight +  '%'});
                movePointLine($_div.find('.pointLine'),startXPx,startYPx,endXPx,endYPx);
            },
            mouseup: function(e){
                isDrawing = false;
                $('.multimediaToTag').removeClass('isDrawing');
                
                var actualId = pointsId;
                message({
                    message: 'titolo di questo tirante:', 
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setPoint(actualId);
                        setPointTitle(actualId, title, true);
                        requirePointDescription(actualId);
                        pointsId++;
                    },
                    onCancel: function(){
                        $(pointPrefixHash+actualId+editorSuffix).remove();
                    }
                });

                $(document).unbind('mousemove');
                $(document).unbind('mouseup');
            }
        })
    };

    var offsetX, offsetY, hotspotX, hotspotXPx, hotspotY, hotspotYPx;
    function startDraggingPoint(e){
        e.preventDefault();
        local = globalToLocal(e);
        startX = local.x;
        startY = local.y;
        startXPx = local.xPx;
        startYPx = local.yPx;
        
        var $_div = $(e.currentTarget).parent();
        var $_hotspot = $_div.find('.pointHotspot')
        var hotspotXPx = $_hotspot.position().left;
        var hotspotYPx = $_hotspot.position().top;
        var hotspotX = hotspotXPx * 100 / mediaWidth;
        var hotspotY = hotspotYPx * 100 / mediaHeight;
        var $_point = $(e.currentTarget);
        var pointWidthPx = $_point.width();
        var pointHeightPx = $_point.height();
        var pointWidth = pointWidthPx * 100 / mediaWidth;
        var pointHeight =  pointHeightPx * 100 / mediaHeight;

        offsetX = (e.pageX - $_point.offset().left - parseInt(pointWidthPx/2)) * 100 / mediaWidth; 
        offsetY = (e.pageY - $_point.offset().top - parseInt(pointHeightPx/2)) * 100 / mediaHeight; 

        if(!$_div.hasClass('locked')){
            var id = $_div.attr('id').split('-')[2];
            
            $(document).bind({
                mousemove: function(e){
                    local = globalToLocal(e);
                    endX = local.x - offsetX;
                    endY = local.y - offsetY;

                    if(endX + pointWidth/2 > 100){
                        endX = 100 - pointWidth/2;
                    } else if (endX - pointWidth/2<0){
                        endX = pointWidth/2;
                    }
                    if(endY + pointHeight/2 > 100){
                        endY = 100 - pointHeight/2;
                    } else if (endY - pointHeight/2<0){
                        endY = pointHeight/2;
                    }
                    
                    $_point.css({ left: endX+"%", top: endY+"%"});

                    endXPx = endX * mediaWidth / 100;
                    endYPx = endY * mediaHeight / 100;
                    
                    var pointXPx = $_point.position().left + parseInt($_point.css('margin-left'));
                    var pointYPx = $_point.position().top + parseInt($_point.css('margin-top'));

                    var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);

                    if($_div.hasClass('connectionLocked')) {
                        endXPx = pointNodes[$_div.data('connectionNodeIndex')][0];
                        endYPx = pointNodes[$_div.data('connectionNodeIndex')][1];
                    } else {
                        var closestEndPoint = findClosestNode(pointNodes,hotspotXPx,hotspotYPx);
                        endXPx = pointNodes[closestEndPoint][0];
                        endYPx = pointNodes[closestEndPoint][1];
                        $_div.data('connectionNodeIndex',closestEndPoint);
                        setPointNode(id)
                    }
                    $_div.find('.pointLineConnection').css({ left: endXPx * 100 / mediaWidth + '%', top: endYPx * 100 / mediaHeight +  '%'});
                    movePointLine($_div.find('.pointLine'),hotspotXPx,hotspotYPx,endXPx,endYPx);
                    //$_mediaToTag.parent().append('<div style="height:4px;width:4px;background-color: #ff00ff; position: absolute; left:'+endX+'%; top:'+endY+'%">')
                    setPointMetrics({'hotspotX':hotspotX,'hotspotY':hotspotY,'pointX':endX,'pointY':endY},id,true);
                },
                mouseup: function(e){
                    $(document).unbind('mousemove');
                    $(document).unbind('mouseup');
                }
            })
        };
    };

    function startDraggingHotspot(e){
        e.preventDefault();
        local = globalToLocal(e);

        var $_div = $(e.currentTarget).parent();
        var $_hotspot = $(e.currentTarget);
        var $_point = $_div.find('.point')
        var pointWidthPx = $_point.width();
        var pointHeightPx = $_point.height();
        var pointWidth = pointWidthPx * 100 / mediaWidth;
        var pointHeight =  pointHeightPx * 100 / mediaHeight;
        var pointXPx = $_point.position().left + parseInt($_point.css('margin-left'));
        var pointYPx = $_point.position().top + parseInt($_point.css('margin-top'));
        var pointX = pointXPx * 100 / mediaWidth;
        var pointY = pointYPx * 100 / mediaHeight;

        if(!$_div.hasClass('locked')){
            var id = $_div.attr('id').split('-')[2];
            
            $(document).bind({
                mousemove: function(e){
                    local = globalToLocal(e);
                    endX = local.x;
                    endY = local.y;

                    if(endX > 100){
                        endX = 100;
                    } else if (endX < 0){
                        endX = 0;
                    }
                    if(endY > 100){
                        endY = 100;
                    } else if (endY < 0){
                        endY = 0;
                    }
                    
                    $_hotspot.css({ left: endX+"%", top: endY+"%"});

                    endXPx = endX * mediaWidth / 100;
                    endYPx = endY * mediaHeight / 100;
                    var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);

                    if($_div.hasClass('connectionLocked')) {
                        connectionXPx = pointNodes[$_div.data('connectionNodeIndex')][0];
                        connectionYPx = pointNodes[$_div.data('connectionNodeIndex')][1];
                    } else {
                        var closestEndPoint = findClosestNode(pointNodes,endXPx,endYPx)
                        connectionXPx = pointNodes[closestEndPoint][0];
                        connectionYPx = pointNodes[closestEndPoint][1];
                        $_div.data('connectionNodeIndex',closestEndPoint);
                        setPointNode(id);
                    }
                    $_div.find('.pointLineConnection').css({ left: connectionXPx * 100 / mediaWidth + '%', top: connectionYPx * 100 / mediaHeight +  '%'});
                    movePointLine($_div.find('.pointLine'),endXPx,endYPx,connectionXPx,connectionYPx);
                    setPointMetrics({'hotspotX':endX,'hotspotY':endY,'pointX':pointX,'pointY':pointY},id,true);
                },
                mouseup: function(e){
                    $(document).unbind('mousemove');
                    $(document).unbind('mouseup');
                }
            })
        };
    };


    function startDraggingConnection(e){
        
        e.preventDefault();
        var startXPx = e.pageX;
        var startYPx = e.pageY;

        var $_div = $(e.currentTarget).parent();
        var previousNodeIndex = $_div.data('connectionNodeIndex')
        var $_hotspot = $_div.find('.pointHotspot');
        var hotspotXPx = $_hotspot.position().left;
        var hotspotYPx = $_hotspot.position().top;
        var $_point = $_div.find('.point');
        var pointWidthPx = $_point.width();
        var pointHeightPx = $_point.height();
        var pointXPx = $_point.position().left + parseInt($_point.css('margin-left'));
        var pointYPx = $_point.position().top + parseInt($_point.css('margin-top'));

        if(!$_div.hasClass('locked')){
            var id = $_div.attr('id').split('-')[2];
            
            $(document).bind({
                mousemove: function(e){
                    local = globalToLocal(e);
                    endX = local.x;
                    endY = local.y;
                    endXPx = local.xPx;
                    endYPx = local.yPx;
                    
                    var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);                 
                    var closestEndPoint = findClosestNode(pointNodes,endXPx,endYPx)
                    endXPx = pointNodes[closestEndPoint][0];
                    endYPx = pointNodes[closestEndPoint][1];

                    $_div.data('connectionNodeIndex',closestEndPoint);
                    $_div.find('.pointLineConnection').css({ left: endXPx * 100 / mediaWidth + '%', top: endYPx * 100 / mediaHeight +  '%'});
                    movePointLine($_div.find('.pointLine'),hotspotXPx,hotspotYPx,endXPx,endYPx);
                },
                mouseup: function(e){
                    if (e.pageX >=startXPx -1 && e.pageX <= startXPx+1 && 
                        e.pageY >=startYPx -1 && e.pageY <= startYPx+1 &&
                        $_div.data('connectionNodeIndex') == previousNodeIndex){
                            $_div.toggleClass('connectionLocked');
                            $_div.find('.pointLineNode.'+nodesNames[$_div.data('connectionNodeIndex')]).toggleClass('lockedNode');
                            if(!$_div.hasClass('connectionLocked')){
                                var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);
                                var closestEndPoint = findClosestNode(pointNodes,hotspotXPx,hotspotYPx);
                                endXPx = pointNodes[closestEndPoint][0];
                                endYPx = pointNodes[closestEndPoint][1];
                                $_div.data('connectionNodeIndex',closestEndPoint);
                                $_div.find('.pointLineConnection').css({ left: endXPx * 100 / mediaWidth + '%', top: endYPx * 100 / mediaHeight +  '%'});
                                movePointLine($_div.find('.pointLine'),hotspotXPx,hotspotYPx,endXPx,endYPx);
                            }
                            setPointNode(id);
                    } else {
                        $_div.addClass('connectionLocked');
                        setPointNode(id);
                        $_div.find('.pointLineNode').removeClass('lockedNode').filter('.'+nodesNames[$_div.data('connectionNodeIndex')]).addClass('lockedNode');
                    }
                    $(document).unbind('mousemove');
                    $(document).unbind('mouseup');
                    updateCodeEditor();
                }

            })
        };
    };
    
    /* Inizio interazione */

    $_mediaToTag.bind({
        dragstart: function(e){
            e.preventDefault();
            if(mode=='drawAreas' && e.which !== 3){ //se siamo in modalità disegno e se non è il tasto destro
                if($('.toolbarMessage').is(':hidden')){
                    startDrawingArea(e);
                } else {
                    focusMessage();
                }
            }
            if(mode=='drawPoints' && e.which !== 3){ //se siamo in modalità disegno e se non è il tasto destro
                if($('.toolbarMessage').is(':hidden')){
                    startDrawingPoint(e);
                } else {
                    focusMessage();
                }
            }
            hideMenus();
        },
        mousedown: function(e){
            if(e.which !== 3){ //se non è il tasto destro
                hideMenus();
            }
        }
    });

    $('.imageArea').live("mousedown", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                if(mode=='drawAreas'){ //se siamo in modalità disegno e se non è il tasto destro
                    startDrawingArea(e);
                } else if(mode=='drawPoints'){
                    startDrawingPoint(e);   
                } else if(mode=='modify') {
                    startDraggingArea(e);
                }
            } else {
                focusMessage();
            }
        }
    });

    $('.areaType').live("click", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                $_clickedArea = $(this).parent();
                if(!$_clickedArea.hasClass('locked') && mode=='modify'){
                    var elementId = parseInt($_clickedArea.attr('id').split('-')[2]);
                    requireAreaType(elementId);
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    $('.areaStyle').live("click", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                $_clickedArea = $(this).parent();
                if(!$_clickedArea.hasClass('locked') && mode=='modify'){
                    var elementId = parseInt($_clickedArea.attr('id').split('-')[2]);
                    requireAreaStyle(elementId)
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    $('.resizeAngle').live("mousedown", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                if(mode=='draw'){ //se siamo in modalità disegno e se non è il tasto destro
                    startDrawingArea(e);
                } else if(mode=='modify'){
                    startResizingAreas(e);
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    $('.removeArea').live("click", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                $_clickedArea = $(this).parent();
                if(!$_clickedArea.hasClass('locked')){
                    var elementId = parseInt($_clickedArea.attr('id').split('-')[2]);
                    message({
                        message: 'sei sicuro di voler rimuovere questa area?', 
                        type: 'confirm',
                        onSubmit: function(){
                            removeArea(elementId);
                        }
                    });
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    $('.point').live("mousedown", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                if(mode=='drawAreas'){ //se siamo in modalità disegno e se non è il tasto destro
                    startDrawingArea(e);
                } else if(mode=='drawPoints'){
                    startDrawingPoint(e);   
                } else if(mode=='modify') {
                    startDraggingPoint(e);
                }
            } else {
                focusMessage();
            }
        }
    });
    
    $('.pointHotspot').live("mousedown", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                if(mode=='drawAreas'){ //se siamo in modalità disegno e se non è il tasto destro
                    startDrawingArea(e);
                } else if(mode=='drawPoints'){
                    startDrawingPoint(e);   
                } else if(mode=='modify') {
                    startDraggingHotspot(e);
                }
            } else {
                focusMessage();
            }
        }
    });

    $('.pointLineConnection').live("mousedown", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                if(mode=='drawAreas'){ //se siamo in modalità disegno e se non è il tasto destro
                    startDrawingArea(e);
                } else if(mode=='drawPoints'){
                    startDrawingPoint(e);   
                } else if(mode=='modify') {
                    startDraggingConnection(e);
                }
            } else {
                focusMessage();
            }
        }
    });

    $('.pointSize').live("click", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                $_clickedArea = $(this).parent().parent();
                if(!$_clickedArea.hasClass('locked') && mode=='modify'){
                    var elementId = parseInt($_clickedArea.attr('id').split('-')[2]);
                    requirePointSize(elementId);
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    $('.removePoint').live("click", function(e){
        if(e.which !== 3){
            hideMenus();
            if($('.toolbarMessage').is(':hidden')){
                $_clickedPoint = $(this).parent().parent();
                if(!$_clickedPoint.hasClass('locked')){
                    var elementId = parseInt($_clickedPoint.attr('id').split('-')[2]);
                    message({
                        message: 'sei sicuro di voler rimuovere questo tirante?', 
                        type: 'confirm',
                        onSubmit: function(){
                            removePoint(elementId);
                        }
                    });
                }
            } else {
                focusMessage();
            }
        }
        return false;
    });

    /* Menu contestuali */
    function hideMenus(){
        $('#areasMenu, #pointsMenu, #mediaMenu','.multimediaToTag').hide();
    }
    $_mediaToTag.live('contextmenu', function(e){
        if(mode!='view'){
            e.preventDefault();
            local = globalToLocal(e);
            startX = local.x;
            startY = local.y;
            position = globalToLocal(e);
            hideMenus();
            $('#mediaMenu','.multimediaToTag').css({
                left: startX + '%',
                top: startY + '%'
            }).show();
            return false;
        }
    });
    
    var $_clickedElement;
    var elementId;
    $('.imageArea').live('contextmenu', function(e){
        $_clickedElement = $(this);
        elementId = parseInt($_clickedElement.attr('id').split('-')[2]);
        var currentOptions = tags.areas[elementId];
        e.preventDefault();
        position = globalToLocal(e);
        hideMenus();
        $('#areasMenu','.multimediaToTag').find('li').removeClass('selected');
        $('#areasMenu','.multimediaToTag').find('.menuItem[action=switchAreaStyle][rel='+currentOptions.style+']').addClass('selected');
        $('#areasMenu','.multimediaToTag').find('.menuItem[action=switchAreaType][rel='+currentOptions.type+']').addClass('selected');
        $('#areasMenu','.multimediaToTag').find('.menuItem[action=switchAreaView][rel='+currentOptions.visible+']').addClass('selected');
        $('#areasMenu','.multimediaToTag').css({
            left: position.x + '%',
            top: position.y + '%'
        }).show();
        return false;
    });
    
    $('.imagePoint').live('contextmenu', function(e){
        $_clickedElement = $(this);
        elementId = parseInt($_clickedElement.attr('id').split('-')[2]);
        var currentOptions = tags.points[elementId];
        e.preventDefault();
        position = globalToLocal(e);
        hideMenus();
        $('#pointsMenu','.multimediaToTag').find('li').removeClass('selected');
        $('#pointsMenu','.multimediaToTag').find('.menuItem[action=switchPointSize][rel='+currentOptions.size+']').addClass('selected');
        $('#pointsMenu','.multimediaToTag').find('.menuItem[action=switchView][rel='+currentOptions.visible+']').addClass('selected');
        $('#pointsMenu','.multimediaToTag').css({
            left: position.x + '%',
            top: position.y + '%'
        }).show();
        return false;
    });

    $('.menuItem','.multimediaToTag').bind('click', function(e){
        e.stopPropagation();
        var action = $(this).attr('action');
        hideMenus();

        switch (action) {

            /* areasMenu */

            case 'switchAreaType':
                var type = $(this).attr('rel');
                setAreaType(elementId, type);
                break;

            case 'switchAreaStyle':
                var style = $(this).attr('rel');
                setAreaStyle(elementId, style);
                break;

            case 'switchAreaView':
                var view = $(this).attr('rel');
                setAreaView(elementId, view);
                break;

            case 'modifyAreaTitle':
                var prevTitle = texts['a-'+elementId].title;
                message({
                    message: 'titolo di questa area:', 
                    type: 'input',
                    value: prevTitle,
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setAreaTitle(elementId,title);
                    }
                });
                break;

            case 'modifyAreaDescription':
                var prevDescription = texts['a-'+elementId].description;
                message({
                    message: 'descrizione di questa area:', 
                    type: 'textarea',
                    value: prevDescription,
                    onSubmit: function(){
                        var description = $('.messageTextarea','.toolbarMessage').val();
                        setAreaDescription(elementId, description);
                    }
                });
                break;

            case 'cloneArea':
                var $_newDiv = $_clickedElement.clone()
                $_newDiv.attr('id',areaPrefix+areasId+editorSuffix);
                $_mediaToTag.parent().append($_newDiv);
                $_newDiv.children('.areaNumber').text(areasId);
                var actualId = areasId;
                message({
                    message: 'titolo di questa area:',
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setArea(actualId)
                        setAreaTitle(actualId, title, true);
                        requireAreaDescription(actualId);
                        areasId++;
                    },
                    onCancel: function(){
                        $(areaPrefixHash+actualId+editorSuffix).remove();
                    }
                });
                break;

            case 'removeArea':
                message({
                    message: 'sei sicuro di voler rimuovere questa area?',
                    type: 'confirm',
                    onSubmit: function(){
                        removeArea(elementId);
                    }
                });
                break;

            case 'firstArea':
                $_clickedElement.css('z-index', ++zIndex);
                tags.areas[elementId].metrics.zIndex = zIndex;
                updateCodeEditor();
                break;

            case 'lastArea':
                $_clickedElement.css('z-index', 0);
                tags.areas[elementId].metrics.zIndex = 0;
                updateCodeEditor();
                break;
            
            case 'forwardArea':
                var newZIndex = parseInt($_clickedElement.css('z-index')) + 1;
                $_clickedElement.css('z-index', newZIndex);
                tags.areas[elementId].metrics.zIndex = newZIndex;
                updateCodeEditor();
                break;
            
            case 'backwardArea':
                var newZIndex = parseInt($_clickedElement.css('z-index')) - 1;
                $_clickedElement.css('z-index', newZIndex);
                tags.areas[elementId].metrics.zIndex = newZIndex;
                updateCodeEditor();
                break;

            /* pointsMenu */

            case 'switchPointSize':
                var size = $(this).attr('rel');
                setPointSize(elementId, size);
                break;

            case 'switchPointView':
                var view = $(this).attr('rel');
                setPointView(elementId, view);
                break;

            case 'modifyPointTitle':
                var prevTitle = texts['c-'+elementId].title;
                message({
                    message: 'titolo di questo tirante:', 
                    type: 'input',
                    value: prevTitle,
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setPointTitle(elementId,title);
                    }
                });
                break;

            case 'modifyPointDescription':
                var prevDescription = texts['c-'+elementId].description;
                message({
                    message: 'descrizione di questo tirante:', 
                    type: 'textarea',
                    value: prevDescription,
                    onSubmit: function(){
                        var description = $('.messageTextarea','.toolbarMessage').val();
                        setPointDescription(elementId, description);
                    }
                });
                break;

            case 'clonePoint':
                var $_newDiv = $_clickedElement.clone()
                $_newDiv.attr('id',pointPrefix+pointsId+editorSuffix);
                $_mediaToTag.parent().append($_newDiv);
                $_newDiv.children('.pointNumber').text(pointsId);
                var actualId = pointsId;
                message({
                    message: 'titolo di questo tirante:',
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setPoint(actualId)
                        setPointTitle(actualId, title, true);
                        requirePointDescription(actualId);
                        pointsId++;
                    },
                    onCancel: function(){
                        $(pointPrefixHash+actualId+editorSuffix).remove();
                    }
                });
                break;

            case 'removePoint':
                message({
                    message: 'sei sicuro di voler rimuovere questo tirante?',
                    type: 'confirm',
                    onSubmit: function(){
                        removePoint(elementId);
                    }
                });
                break;

            case 'firstPoint':
                $_clickedElement.css('z-index', ++zIndex);
                tags.points[elementId].metrics.zIndex = zIndex;
                updateCodeEditor();
                break;

            case 'lastPoint':
                $_clickedElement.css('z-index', 0);
                tags.points[elementId].metrics.zIndex = 0;
                updateCodeEditor();
                break;
            
            case 'forwardPoint':
                var newZIndex = parseInt($_clickedElement.css('z-index')) + 1;
                $_clickedElement.css('z-index', newZIndex);
                tags.points[elementId].metrics.zIndex = newZIndex;
                updateCodeEditor();
                break;
            
            case 'backwardPoint':
                var newZIndex = parseInt($_clickedElement.css('z-index')) - 1;
                $_clickedElement.css('z-index', newZIndex);
                tags.points[elementId].metrics.zIndex = newZIndex;
                updateCodeEditor();
                break;
            
            /* Generale */

            case 'lockUnlock':
                $_clickedElement.toggleClass('locked');
                break;
            
            case 'hide':
                $_clickedElement.addClass('hide');
                break;

            /* mediaMenu */

            case 'createArea':
                var leftPos = startX - 5;
                var topPos = startY - 5;
                createArea(areasId,"left:"+leftPos+"%;top:"+topPos+"%;width:10%;height:10%;z-index:"+zIndex);
                var actualId = areasId;
                message({
                    message:'titolo di questa area:',
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setArea(actualId);
                        setAreaTitle(actualId, title, true);
                        requireAreaDescription(actualId);
                        areasId++;
                    },
                    onCancel: function(){
                        $(areaPrefixHash+actualId+editorSuffix).remove();
                    }
                });

                break;
            
            case 'createPoint':
                var leftPos = startX;
                var topPos = startY;
                createPoint(pointsId,{'hotspotX': leftPos,'hotspotY': topPos}, zIndex);
                var actualId = pointsId;
                message({
                    message:'titolo di questo tirante:',
                    type: 'input',
                    onSubmit: function(){
                        var title = $('.messageInput','.toolbarMessage').val();
                        setPoint(actualId);
                        setPointTitle(actualId, title, true);
                        requirePointDescription(actualId);
                        pointsId++;
                    },
                    onCancel: function(){
                        $(pointPrefixHash+actualId+editorSuffix).remove();
                    }
                });

                break;
            
            case 'removeAll':
                message({
                    message: 'sei sicuro di voler rimuovere tutto?',
                    type: 'confirm',
                    onSubmit: function(){
                        $('.imageArea','.multimediaToTag').remove();
                        $('.imagePoint','.multimediaToTag').remove();
                        areasId = 0;
                        for(var areaId in tags.areas){
                            if(tags.areas.hasOwnProperty(areaId)){
                                delete tags.areas[areaId];
                            }
                            if(texts.hasOwnProperty('a-'+areaId)){
                                delete texts['a-'+areaId];
                            }
                        }
                        pointsId = 0;
                        for(var pointId in tags.points){
                            if(tags.points.hasOwnProperty(pointId)){
                                delete tags.points[pointId];
                            }
                            if(texts.hasOwnProperty('c-'+pointId)){
                                delete texts['c-'+pointId];
                            }
                        }
                        updateCodeEditor();
                    }
                });
                break;
                
            case 'lockAll':
                $('.imageArea, .imagePoint','.multimediaToTag').addClass('locked');
                break;

            case 'unlockAll':
                $('.imageArea, .imagePoint','.multimediaToTag').removeClass('locked');
                break;

            case 'hideAll':
                $('.imageArea, .imagePoint','.multimediaToTag').addClass('hide');
                break;

            case 'showAll':
                $('.imageArea, .imagePoint','.multimediaToTag').removeClass('hide');
                break;
        }
    });
    
    /* Gestione codice, contenuto, oggetti  - Aree */

    function createArea(id,metrics){
        if(typeof metrics !== 'undefined'){
            var newDiv = '<div class="imageArea" id="'+areaPrefix+id+editorSuffix+'" style="'+metrics+'">';
        } else {
            var newDiv = '<div class="imageArea" id="'+areaPrefix+id+editorSuffix+'">';
        }
        newDiv += ' <div class="removeArea">x</div>';
        newDiv += ' <div class="areaTitle">Titolo</div>';
        newDiv += ' <div class="areaDescription">Descrizione</div>';
        newDiv += ' <div class="resizeAngle angleNE"></div>';
        newDiv += ' <div class="resizeAngle angleSE"></div>';
        newDiv += ' <div class="resizeAngle angleSW"></div>';
        newDiv += ' <div class="resizeAngle angleNW"></div>';
        newDiv += ' <div class="areaStyle"></div>';
        newDiv += ' <div class="areaType"></div>';
        newDiv += ' <div class="areaNumber">'+id+'</div>';
        newDiv += '</div>';
        var r = $(newDiv);
        $_mediaToTag.parent().append(r);
        return r;
    }

    function setArea(id){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        tags.areas[id] = {};
        tags.areas[id].id = areaPrefix+id;
        tags.areas[id].metrics = setAreaMetrics({
            left: parseFloat($_area.css('left')),
            top: parseFloat($_area.css('top')),
            width: parseFloat($_area.css('width')),
            height: parseFloat($_area.css('height')),
            zIndex: parseInt($_area.css('z-index'))
        })
        texts['a-'+id] = {};
        texts['a-'+id].id = areaPrefix+id;
        
        updateCodeEditor();
    }

    function removeArea(elementId){
        if(elementId != areasId-1){
            for(var prevId = elementId + 1; prevId < areasId; prevId++){
                var nextId = prevId - 1;
                swapAreaIds(prevId,nextId);
            }
        } else {
            delete tags.areas[elementId];
            delete texts['a-'+elementId];
        }
        areasId--;
        $(areaPrefixHash+elementId+editorSuffix).remove();
        updateCodeEditor();
    }

    function swapAreaIds(from, to){
        tags.areas[from].id = areaPrefix + to;
        tags.areas[to] = tags.areas[from];
        delete tags.areas[from];
        texts['a-'+from].id = areaPrefix + to;
        texts['a-'+to] = texts['a-'+from];
        delete texts['a-'+from];
        $(areaPrefixHash+from+editorSuffix)
            .attr('id', areaPrefix+to+editorSuffix)
            .children('.areaNumber').text(to);
    }

    function setAreaDescription(id,description,isFirst){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        if(description==''){
            description = 'Descrizione';
        }
        if(isFirst){
            //Correggo primo caricamento della descrizione per tinyMce
            texts['a-'+id].description = '<p>' + description + '</p>';
            $_area.children('.areaDescription').html('<p>' + description + '</p>');
        } else {
            texts['a-'+id].description = description;
            $_area.children('.areaDescription').html(description);
        }
        updateCodeEditor();
    }

    function setAreaTitle(id,title,isFirst){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        if(title==''){
            title = 'Titolo';
        }
        texts['a-'+id].title = title;
        if(isFirst){
            texts['a-'+id].description = '<p>Descrizione</p>';
        }
        $_area.children('.areaTitle').html(title);
        updateCodeEditor();
    }

    function setAreaType(id,type,isFirst){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        if(type==''){
            type = areaTypes[0];
        }
        tags.areas[id].type = type;
        /*if(isFirst){
            texts[id].description = '<p>Descrizione</p>';
        }*/
        tempTypes = areaTypes.slice();
        for(var i = tempTypes.length-1; i>=0; i--){
            if(tempTypes[i] == type){
                tempTypes.splice(i,1);
            }
        }
        $_area
            .removeClass(tempTypes.join(' '))
            .addClass(type)
            .children('.areaType')
            .removeClass(tempTypes.join(' '))
            .addClass(type);

        updateCodeEditor();
    }

    function setAreaStyle(id,style,isFirst){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        if(style==''){
            style = areaStyles[0];
        }
        tags.areas[id].style = style;
        if(isFirst){
            tags.areas[id].visible = 'false';
        }
        tempStyles = areaStyles.slice();
        for(var i = tempStyles.length-1; i>=0; i--){
            if(tempStyles[i] == style){
                tempStyles.splice(i,1);
            }
        }
        $_area
            .removeClass(tempStyles.join(' '))
            .addClass(style)
            .children('.areaStyle')
            .removeClass(tempStyles.join(' '))
            .addClass(style);

        updateCodeEditor();
    }

    function setClass(id,className,isFirst){
        var $_area = $(areaPrefixHash+id+editorSuffix);
        if(className==''){
            className = areaClasses[0];
        }
        tags.areas[id].class = style;
        if(isFirst){
            tags.areas[id].visible = 'false';
        }
        tempClasses = areaClasses.slice();
        for(var i = tempStyles.length-1; i>=0; i--){
            if(tempClasses[i] == style){
                tempClasses.splice(i,1);
            }
        }
        $_area
            .removeClass(tempClasses.join(' '))
            .addClass(className);
            /*.children('.areaStyle')
            .removeClass(tempClasses.join(' '))
            .addClass(className);*/

        updateCodeEditor();
    }

    function setAreaView(id,view){
        tags.areas[id].visible = view;
        updateCodeEditor();
    }

    function requireAreaDescription(id){
        message({
            message: 'testo di questa area:',
            type: 'textarea',
            onSubmit: function(){
                var description = $('.messageTextarea','.toolbarMessage').val();
                setAreaDescription(id,description,true);
                requireAreaType(id);
            },
            onCancel: function(){
                setAreaDescription(id,'',true);
                requireAreaType(id);
            }
        });
    }

    function requireAreaType(id){
        message({
            message: 'posizione del testo:',
            type: 'options',
            options: [
                {value: 'tooltip', label: 'relativo all\'area', checked: true },
                {value: 'modal', label: 'relativo all\'immagine'},
                {value: 'postit', label: 'post-it'}
            ],
            onSubmit: function(){
                var type = $('.messageOption:input:checked','.toolbarMessage').attr('value');
                setAreaType(id,type,true);
                requireAreaStyle(id);
            },
            onCancel: function(){
                setAreaType(id,areaTypes[0],true);
                requireAreaStyle(id);
            }
        });
    }

    function requireAreaStyle(id){
        message({
            message: 'stile:',
            type: 'options',
            options: [
                {value: 'dotted', label: 'icona', checked: true },
                {value: 'bordered', label: 'area con bordo'},
                {value: 'filled', label: 'area piena'},
                {value: 'hidden', label: 'nascosto'}
            ],
            onSubmit: function(){
                var style = $('.messageOption:input:checked','.toolbarMessage').attr('value');
                setAreaStyle(id,style,true);
            },
            onCancel: function(){
                setAreaStyle(id,areaStyles[0],true);
            }
        });
    }

    /* Gestione codice, contenuto, oggetti - Tiranti */

    var createPoint = function(id,elementsPositions,metrics){
        if(typeof metrics !== 'undefined'){
            var newDiv = '<div class="imagePoint" id="'+pointPrefix+id+editorSuffix+'" style="'+metrics+'">';
        } else {
            var newDiv = '<div class="imagePoint" id="'+pointPrefix+id+editorSuffix+'">';
        }
        if(!elementsPositions.hasOwnProperty('pointX')) {
            elementsPositions.pointX = elementsPositions.hotspotX;
            elementsPositions.pointY = elementsPositions.hotspotY;
        }
        newDiv += ' <div class="pointHotspot" style="z-index:4;left: '+elementsPositions.hotspotX+'%; top:'+elementsPositions.hotspotY+'%"></div>';
        var transform = 'translate('+elementsPositions.hotspotX+'px,'+elementsPositions.hotspotX+'px)';
        newDiv += ' <div class="pointLine" style="z-index:1;width:0px;-webkit-transform:'+transform+';-moz-transform:'+transform+';-ms-transform:'+transform+';-o-transform:'+transform+'; transform:'+transform+'"></div>'
        newDiv += ' <div class="pointLineConnection" style="z-index:3"></div>';
        newDiv += ' <div class="point medium" style="z-index: 2; left: '+ elementsPositions.pointX+'%; top:'+elementsPositions.pointY+'%">';
        newDiv += '     <div class="removePoint">x</div>';
        newDiv += '     <div class="pointTitle">Titolo</div>';
        newDiv += '     <div class="pointDescription">Descrizione</div>';
        newDiv += '     <div class="pointSize medium">M</div>';
        newDiv += '     <div class="pointNumber">'+id+'</div>';
        newDiv += '     <div class="pointLineNode n"></div>';
        newDiv += '     <div class="pointLineNode ne"></div>';
        newDiv += '     <div class="pointLineNode e"></div>';
        newDiv += '     <div class="pointLineNode se"></div>';
        newDiv += '     <div class="pointLineNode s"></div>';
        newDiv += '     <div class="pointLineNode sw"></div>';
        newDiv += '     <div class="pointLineNode w"></div>';
        newDiv += '     <div class="pointLineNode nw"></div>';
        newDiv += '     <div class="pointLineNode c"></div>';
        newDiv += ' </div>';
        newDiv += '</div>';
        var r = $(newDiv);
        $_mediaToTag.parent().append(r);
        return r;
    };

    function setPoint(id){
        var $_point = $(pointPrefixHash+id+editorSuffix);
        tags.points[id] = {};
        tags.points[id].id = pointPrefix+id;
        var roundTo = 2; // decimali
        tags.points[id].metrics = {
            hotspotX: parseFloat($_point.find('.pointHotspot').css('left')).toFixed(roundTo),
            hotspotY: parseFloat($_point.find('.pointHotspot').css('top')).toFixed(roundTo),
            pointX: parseFloat($_point.find('.point').css('left')).toFixed(roundTo),
            pointY: parseFloat($_point.find('.point').css('top')).toFixed(roundTo),
            zIndex: parseInt($_point.css('z-index'))
        };

        tags.points[id].node = nodesNames[$_point.data('connectionNodeIndex')];
        tags.points[id].locked = $_point.hasClass('connectionLocked');
        texts['c-'+id] = {};
        texts['c-'+id].id = pointPrefix+id;
        
        updateCodeEditor();
    }

    function setPointNode(id){
        var $_point = $(pointPrefixHash+id+editorSuffix);
        tags.points[id].node = nodesNames[$_point.data('connectionNodeIndex')];
        tags.points[id].locked = $_point.hasClass('connectionLocked');
        updateCodeEditor();
    }

    function removePoint(elementId){
        if(elementId != pointsId-1){
            for(var prevId = elementId + 1; prevId < pointsId; prevId++){
                var nextId = prevId - 1;
                swapPointIds(prevId,nextId);
            }
        } else {
            delete tags.points[elementId];
            delete texts['c-'+elementId];
        }
        pointsId--;
        $(pointPrefixHash+elementId+editorSuffix).remove();
        updateCodeEditor()
    }

    function swapPointIds(from, to){
        tags.points[from].id = pointPrefix + to;
        tags.points[to] = tags.points[from];
        delete tags.points[from];
        texts['c-'+from].id = pointPrefix + to;
        texts['c-'+to] = texts['c-'+from];
        delete texts['c-'+from];
        $(pointPrefixHash+from+editorSuffix)
            .attr('id', pointPrefix+to+editorSuffix)
            .find('.pointNumber').text(to);
    }

    function setPointDescription(id,description,isFirst){
        var $_point = $(pointPrefixHash+id+editorSuffix);
        if(description==''){
            description = 'Descrizione';
        }
        if(isFirst){
            //Correggo primo caricamento della descrizione per tinyMce
            texts['c-'+id].description = '<p>' + description + '</p>';
            $_point.find('.pointDescription').html('<p>' + description + '</p>');
        } else {
            texts['c-'+id].description = description;
            $_point.find('.pointDescription').html(description);
        }
        updateCodeEditor();
    }

    function setPointTitle(id,title,isFirst){
        var $_point = $(pointPrefixHash+id+editorSuffix);
        if(title==''){
            title = 'Titolo';
        }
        texts['c-'+id].title = title;
        if(isFirst){
            texts['c-'+id].description = '<p>Descrizione</p>';
        }
        $_point.find('.pointTitle').html(title);
        updateCodeEditor();
    }

    function setPointSize(id,size,isFirst){
        var $_div = $(pointPrefixHash+id+editorSuffix);
        if(size==''){
            size = pointSizes[2];
        }
        tags.points[id].size = size;
        sizeIndex = 0;
        for(var i = pointSizes.length-1; i>=0; i--){
            if(pointSizes[i] == size){
                sizeIndex = i;
            }
        }
        $_div.find('.point')
            .removeClass(pointSizes.join(' '))
            .addClass(size)
            .children('.pointSize')
            .removeClass(pointSizes.join(' '))
            .addClass(size)
            .text(pointSizesInitials[sizeIndex]);

        var $_hotspot = $_div.find('.pointHotspot');
        var hotspotXPx = $_hotspot.position().left;
        var hotspotYPx = $_hotspot.position().top;
        var $_point = $_div.find('.point');
        var pointWidthPx = $_point.width();
        var pointHeightPx = $_point.height();
        var pointWidth = pointWidthPx * 100 / mediaWidth;
        var pointHeight =  pointHeightPx * 100 / mediaHeight;
        var pointXPx = $_point.position().left;
        var pointYPx = $_point.position().top;
        var pointX = pointXPx * 100 / mediaWidth;
        var pointY = pointYPx * 100 / mediaHeight;

        if(pointX + pointWidth/2 > 100){
            pointX = 100-pointWidth/2;
        } else if (pointX - pointWidth/2 < 0){
            pointX = pointWidth/2;
        }
        if(pointY + pointHeight/2 > 100){
            pointY = 100 - pointHeight/2;
        } else if (pointY - pointHeight/2 < 0){
            pointY = pointHeight/2;
        }
        $_point.css({ left: pointX+"%", top: pointY+"%"});

        pointXPx = (pointX * mediaWidth / 100) + parseInt($_point.css('margin-left'));
        pointYPx = (pointY * mediaHeight / 100) + parseInt($_point.css('margin-top'));
        var pointNodes = findNodesCoords(pointXPx,pointYPx,pointWidthPx,pointHeightPx);
        if($_div.hasClass('connectionLocked')) {
            nodeXPx = pointNodes[$_div.data('connectionNodeIndex')][0];
            nodeYPx = pointNodes[$_div.data('connectionNodeIndex')][1];
        } else {
            var closestEndPoint = findClosestNode(pointNodes,hotspotXPx,hotspotYPx)
            nodeXPx = pointNodes[closestEndPoint][0];
            nodeYPx = pointNodes[closestEndPoint][1];
            $_div.data('connectionNodeIndex',closestEndPoint);
            setPointNode(id)
        }
        $_div.find('.pointLineConnection').css({ left: nodeXPx * 100 / mediaWidth + '%', top: nodeYPx * 100 / mediaHeight +  '%'});
        movePointLine($_div.find('.pointLine'),hotspotXPx,hotspotYPx,nodeXPx,nodeYPx);

        updateCodeEditor();
    }

    function setPointView(id,view){
        tags.points[id].visible = view;
        updateCodeEditor();
    }

    function requirePointDescription(id){
        message({
            message: 'testo di questo tirante:',
            type: 'textarea',
            onSubmit: function(){
                var description = $('.messageTextarea','.toolbarMessage').val();
                setPointDescription(id,description,true);
                requirePointSize(id);
            },
            onCancel: function(){
                setPointDescription(id,'',true);
                requirePointSize(id);
            }
        });
    }

    function requirePointSize(id){
        message({
            message: 'dimensione:',
            type: 'options',
            options: [
                {value: 'xsmall', label: 'xsmall'},
                {value: 'small', label: 'small'},
                {value: 'medium', label: 'medium', checked: true},
                {value: 'large', label: 'large'},
                {value: 'xlarge', label: 'xlarge'}
            ],
            onSubmit: function(){
                var size = $('.messageOption:input:checked','.toolbarMessage').attr('value');
                setPointSize(id,size,true);
            },
            onCancel: function(){
                setPointSize(id,pointSizes[2],true);
            }
        });
    }


    /* Funzioni per l'update */
    
    function updateCodeEditor(){
        $("textarea[name='data[abstract]']").val($.stringify(tags, '    '));
        $("textarea[name='data[body]']").val($.stringify(texts, '   '));
        updateContentEditor();
    }

    function updateContentEditor(){
        var content = '';
        for (var textId in texts){
            if(texts.hasOwnProperty(textId)){
                currentText = texts[textId];
                number = currentText.id.split('-')[2];
                type = textId.split('-')[0];
                switch(type){
                    case 'a':
                        type = 'area';
                        typeName = 'area';
                        break;
                    case 'c':
                        type = 'point';
                        typeName = 'tirante'
                        break;
                }
                content += '<div class="imageTag ' + type + '" id="' + currentText.id+'" rel="'+textId+'">';
                    content += '<div class="tagTitleHolder '+typeName+'_title">';
                    content += currentText.title;
                    content += '</div>';
                    //content += '<div class="tagDesc">';
                    var desc = (currentText.description || '').replace('<p>','');
                    desc = desc.replace('</p>','');
                    desc = '<p >' + desc + '</p>'
                    content += desc;
                    //content += '</div>';
                content += '</div>';
            }
        }
        var randomEl = CKEDITOR.instances['multimediaAdvanceTextArea'].getSelection().getNative().anchorNode;
        if (!$(randomEl).hasClass('cke_editable')) randomEl = $(randomEl).closest('body.cke_editable')
        $(randomEl).html(content);
    }

    function updateFromCode(){
        parseTags = $.parseJSON($("textarea[name='data[abstract]']").text());
        if(parseTags){
            
            /* aree */
            
            actualId = 0;
            elementsNumber = 0;
            for (var areaNumber in parseTags.areas) {
                if(parseTags.areas.hasOwnProperty(areaNumber)) {
                    var area = parseTags.areas[areaNumber];
                    var metrics = setAreaMetrics(area.metrics, areaNumber, false, true);
                    if(typeof metrics.zIndex === 'undefined'){
                        metrics.zIndex = zIndex;
                    }
                    
                    $_area = $('#'+area.id+editorSuffix);
                    
                    if($_area.length>0){
                        $_area.css({
                            left: metrics.left + '%',
                            top: metrics.top + '%',
                            width: metrics.width + '%',
                            height: metrics.height + '%',
                            zIndex: metrics.zIndex
                        });
                        if(texts.hasOwnProperty('a-'+areaNumber)){
                            $_area.children('.areaTitle').html(texts['a-'+areaNumber].title);
                            $_area.children('.areaDescription').html(texts['a-'+areaNumber].description);
                        }
                    } else {
                        $_area = createArea(areaNumber,"left:"+metrics.left+"%;top:"+metrics.top+"%;width:"+metrics.width+"%;height:"+metrics.height+"%;z-index:"+metrics.zIndex);
                        if(texts.hasOwnProperty('a-'+areaNumber)){
                            $_area.children('.areaTitle').html(texts['a-'+areaNumber].title);
                            $_area.children('.areaDescription').html(texts['a-'+areaNumber].description);
                        } else {
                            $_area.children('.areaTitle').html('Titolo');
                            $_area.children('.areaDescription').html('Descrizione');
                        }
                        areasId++;
                    }
                    $_area
                        .removeClass(areaTypes.join(' '))
                        .addClass(area.type)
                        .children('.areaType')
                        .removeClass(areaTypes.join(' '))
                        .addClass(area.type);

                    $_area
                        .removeClass(areaStyles.join(' '))
                        .addClass(area.style)
                        .children('.areaStyles')
                        .removeClass(areaStyles.join(' '))
                        .addClass(area.style);
                    
                    tags.areas[areaNumber] = parseTags.areas[areaNumber]
                    
                    if(parseInt(areaNumber) != elementsNumber){
                        if($(areaPrefixHash+elementsNumber+editorSuffix).length>0){
                            $(areaPrefixHash+elementsNumber+editorSuffix).remove();
                        }
                        swapAreaIds(areaNumber, elementsNumber);
                        updateCodeEditor();
                    }
                    elementsNumber++;
                }
            }
            for (var areaNumber in tags.areas) {
                if (tags.areas.hasOwnProperty(areaNumber)) {
                    if(parseInt(areaNumber)>elementsNumber-1){
                        delete tags.areas[areaNumber];
                        delete texts[areaNumber];
                        $(areaPrefixHash+areaNumber+editorSuffix).remove();
                        updateCodeEditor();
                    }
                }
            }

            /* tiranti */

            actualId = 0;
            elementsNumber = 0;
            for (var pointNumber in parseTags.points) {
                if(parseTags.points.hasOwnProperty(pointNumber)) {
                    var point = parseTags.points[pointNumber];
                    var metrics = point.metrics;
                    if(typeof metrics.zIndex === 'undefined'){
                        metrics.zIndex = zIndex;
                    }
                    $_point = $('#'+point.id+editorSuffix);
                    if($_point.length>0){
                        $_point.children('.pointHotspot').css({
                            left: metrics.hotspotX + '%',
                            top: metrics.hotspotY + '%'
                        });
                        $_point.children('.pointHotspot').css({
                            left: metrics.hotspotX + '%',
                            top: metrics.hotspotY + '%'
                        });
                        if(texts.hasOwnProperty('c-'+pointNumber)){
                            $_point.find('.pointTitle').html(texts['c-'+pointNumber].title);
                            $_point.find('.pointDescription').html(texts['c-'+pointNumber].description);
                        }
                    } else {
                        $_point = createPoint(pointNumber,{'hotspotX': metrics.hotspotX,'hotspotY':metrics.hotspotY,'pointX':metrics.pointX,'pointY':metrics.pointY},'z-index:' + metrics.zIndex);
                        if(texts.hasOwnProperty('c-'+pointNumber)){
                            $_point.find('.pointTitle').html(texts['c-'+pointNumber].title);
                            $_point.find('.pointDescription').html(texts['c-'+pointNumber].description);
                        } else {
                            $_point.find('.pointTitle').html('Titolo');
                            $_point.find('.pointDescription').html('Descrizione');
                        }
                        pointsId++;
                    }
                    nodeIndex = 0;
                    for(var i = 0; i < nodesNames.length; i++){
                        if(nodesNames[i] == point.node){
                            nodeIndex = i;  
                        }
                    }
                    //$_point = $('#'+point.id+editorSuffix);
                    $_point.data('connectionNodeIndex',nodeIndex);
                    if(point.locked){
                        $_point.addClass('connectionLocked').find('.pointLineNode').removeClass('lockedNode').filter('.'+nodesNames[nodeIndex]).addClass('lockedNode');
                    }

                    tags.points[pointNumber] = parseTags.points[pointNumber];
                    
                    setPointSize(pointNumber,point.size,false);
                    
                    if(parseInt(pointNumber) != elementsNumber){
                        if($(pointPrefixHash+elementsNumber+editorSuffix).length>0){
                            $(pointPrefixHash+elementsNumber+editorSuffix).remove();
                        }
                        swapAreaIds(pointNumber, elementsNumber);
                        updateCodeEditor();
                    }
                    elementsNumber++;
                }
            }
            for (var pointNumber in tags.points) {
                if (tags.points.hasOwnProperty(pointNumber)) {
                    if(parseInt(pointNumber)>elementsNumber-1){
                        delete tags.points[pointNumber];
                        delete texts[pointNumber];
                        $(pointPrefixHash+pointNumber+editorSuffix).remove();
                        updateCodeEditor();
                    }
                }
            }
        }
    }

    function setPreview(){
        $_mediaToTag.parent().data("tags",$.stringify(tags));
        $_mediaToTag.parent().data("texts",$.stringify(texts));
        $_mediaToTag.parent().data("mediaid",mediaId);
        $('.multimediaToTag').tagImage();
    }

    /* Update */

    // Primo caricamento
    if($("textarea[name='data[abstract]']").val() !=''){
        texts =  $.parseJSON($("textarea[name='data[body]']").text());
        updateFromCode();
        for(var textNumber in texts){
            if(textNumber.split('-').length<2){
                texts['a-'+textNumber] = texts[textNumber];
                delete texts[textNumber];
            }
        }
    }
    
    // TinyMce non sembra esporre "onChange" fuori dall'init, quindi metto un setInterval
    //setInterval(updateFromContent,500);
    
    function loadFromJson() {
        var json = $("textarea[name='data[body]']").text() || $("textarea[name='data[body]']").val() || '';
        var myTag = $.parseJSON(json);
        if (myTag!=null) texts = myTag;
        updateContentEditor();
    }
    
    loadFromJson();

    // TinyMce non espone oninit, quindi metto un setInterval e lo cancello appena l'editor è caricato 
    /*function firstUpdateContentEditor () {
        if(CKEDITOR && typeof CKEDITOR.instances['data[content]'] !=='undefined'){
            clearInterval(firstContent);
            
        }
    }*/
    //var firstContent = setInterval(firstUpdateContentEditor,250);
    // Live editing del codice
        
    function changedHtml(ev){
        setTimeout(function() {
            var html = CKEDITOR.instances['multimediaAdvanceTextArea'].getData();
            var sDiv = $('<div></div>');
            sDiv.append(html);
            sDiv.find('.imageTag').each( function () {
                var rel = $(this).attr('rel');
                var title = $(this).find('.tagTitleHolder').html();
                var desc = $(this).find('p').html();
                texts[rel].title = title;
                texts[rel].description = desc;
            });
            
            if (sDiv.find('.imageTag').length>0) {
                $("textarea[name='data[body]']").text($.stringify(texts, '  '));
                $("textarea[name='data[body]']").val($.stringify(texts, '   '));
            } else {
                texts = {};
                tags = {};
                $("textarea[name='data[body]']").text('');
                $("textarea[name='data[abstract]']").text('');
                $("textarea[name='data[body]']").val('');
                $("textarea[name='data[abstract]']").val('');
            }
            updateFromCode();
        },500);
    }   
    
    $("textarea[name='data[abstract]']").keyup(function (event) {
        updateFromCode();
    });
    
    /* Utilities */

    function setAreaMetrics(metrics, areaId, updateCode, showMessages){
        var messages = [];
        var oldmetrics = metrics;

        if(metrics.width<areaMinWidth){
            messages.push('larghezza inferiore al minimo consentito (' + areaMinWidth +'%)');
            metrics.width = areaMinWidth;
        } else if(metrics.width>areaMaxWidth){
            messages.push('larghezza superiore al massimo consentito (' + areaMaxWidth +'%)');
            metrics.width = areaMaxWidth;
        }
        if(metrics.height<areaMinHeight){
            messages.push('altezza inferiore al minimo consentito (' + areaMinHeight +'%)');
            metrics.height = metrics.areaMinHeight;
        } else if(metrics.height>metrics.areaMaxHeight){
            messages.push('altezza superiore al massimo consentito (' + areaMaxHeight +'%)');
            metrics.height = areaMaxHeight;
        }

        if(metrics.left<0){
            messages.push('coordinata left fuori dal media');
            metrics.left = 0;
        } else if(parseFloat(metrics.left) + parseFloat(metrics.width)>100){
            messages.push('larghezza fuori dal media, coordinata left spostata');
            metrics.left = 100 -  metrics.width;
        }
        if(metrics.top<0){
            messages.push('coordinata top fuori dal media');
            metrics.top = 0;
        } else if(parseFloat(metrics.top) + parseFloat(metrics.height)>100){
            messages.push('altezza fuori dal media, coordinata top spostata');
            metrics.top = 100 - metrics.height;
        }

        var roundTo = 2; // decimali
        metrics.left = parseFloat(metrics.left).toFixed(roundTo);
        metrics.top = parseFloat(metrics.top).toFixed(roundTo);
        metrics.width = parseFloat(metrics.width).toFixed(roundTo);
        metrics.height = parseFloat(metrics.height).toFixed(roundTo);

        if(typeof areaId !== 'undefined' && tags.areas.hasOwnProperty(areaId)){
            tags.areas[areaId].metrics = metrics;
            updateCodeEditor();
        }
        if(typeof updateCode !== 'undefined' && updateCode){
            updateCodeEditor();
        }

        if(messages.length>0 && typeof showMessages !== 'undefined' && showMessages){
            updateCodeEditor();
            message('Attenzione: <br> - ' + messages.join('<br> - '));
        }
        return metrics;
    }

    function setPointMetrics(metrics, pointId, updateCode, showMessages){
        var messages = [];
        var oldmetrics = metrics;

        if(metrics.hotspotX < 0){
            messages.push('coordinata x di hotspot minore di 0');
            metrics.hotspotX = 0;
        } else if(metrics.hotspotX>100){
            messages.push('coordinata x di hotspot maggiore di 100');
            metrics.hotspotX = 100;
        }
        if(metrics.hotspotY < 0){
            messages.push('coordinata y di hotspot minore di 0');
            metrics.hotspotY = 0;
        } else if(metrics.hotspotY>100){
            messages.push('coordinata y di hotspot maggiore di 100');
            metrics.hotspotY = 100;
        }

        if(metrics.pointX < 0){
            messages.push('coordinata x di point minore di 0');
            metrics.pointX = 0;
        } else if(metrics.pointX>100){
            messages.push('coordinata x di point maggiore di 100');
            metrics.pointX = 100;
        }
        if(metrics.pointY < 0){
            messages.push('coordinata y di point minore di 0');
            metrics.pointY = 0;
        } else if(metrics.pointY>100){
            messages.push('coordinata y di point maggiore di 100');
            metrics.pointY = 100;
        }

        var roundTo = 2; // decimali
        metrics.hotspotX = parseFloat(metrics.hotspotX).toFixed(roundTo);
        metrics.hotspotY = parseFloat(metrics.hotspotY).toFixed(roundTo);
        metrics.pointX = parseFloat(metrics.pointX).toFixed(roundTo);
        metrics.pointY = parseFloat(metrics.pointY).toFixed(roundTo);

        if(typeof pointId !== 'undefined' && tags.points.hasOwnProperty(pointId)){
            tags.points[pointId].metrics = metrics;
        }
        if(typeof updateCode !== 'undefined' && updateCode){
            updateCodeEditor();
        }

        if(messages.length>0 && typeof showMessages !== 'undefined' && showMessages){
            updateCodeEditor();
            message('Attenzione: <br> - ' + messages.join('<br> - '));
        }

        return metrics;
    }
    function findNodesCoords(boxXPx,boxYPx,boxWidthPx,boxHeightPx){
        return [
            [boxXPx+boxWidthPx/2,boxYPx], //n
            [boxXPx+boxWidthPx,boxYPx], //ne
            [boxXPx+boxWidthPx,boxYPx+boxHeightPx/2], //e
            [boxXPx+boxWidthPx,boxYPx+boxHeightPx], //se
            [boxXPx+boxWidthPx/2,boxYPx+boxHeightPx], //s
            [boxXPx,boxYPx+boxHeightPx], //sw
            [boxXPx,boxYPx+boxHeightPx/2], //w
            [boxXPx,boxYPx], //nw
            [boxXPx+boxWidthPx/2,boxYPx+boxHeightPx/2] //c
        ];
    }

    function findClosestNode(nodesCoords, targetXPx, targetYPx){
        var closestEndPoint = 0;
        var closestDistance = 10000;
        for(var i = 0; i < nodesCoords.length; i++){
            nodeXPx = nodesCoords[i][0];
            nodeYPx = nodesCoords[i][1];
            var distance = Math.sqrt(Math.pow(nodeXPx - targetXPx,2) + Math.pow(nodeYPx-targetYPx,2));
            if(distance<closestDistance){
                closestDistance = distance;
                closestEndPoint = i;
            }
        }
        return closestEndPoint;
    }

    function movePointLine($_line,startXPx,startYPx,endXPx,endYPx) {
        var angleRadians = Math.atan2(endYPx-startYPx,endXPx-startXPx);
        var angle = angleRadians/Math.PI*180;
        var distance = Math.sqrt(Math.pow(endXPx - startXPx,2) + Math.pow(endYPx - startYPx,2));
        var transformX = (startXPx+((distance/2)*Math.cos(angleRadians) - (distance/2))).toFixed();
        var transformY = (startYPx+((distance/2)*Math.sin(angleRadians))).toFixed();
        var transform = 'translate(' + transformX + 'px, '+ transformY+'px) rotate('+angle+'deg)';
        $_line.css({
            width: distance + 'px',
            '-webkit-transform': transform,
            '-moz-transform': transform,
            '-ms-transform': transform,
            '-o-transform': transform,
            transform: transform
        });
    }

    function message(message){
        if(typeof message === 'string'){
            $('.toolbarMessage','.multimediaToTag')
                .clearQueue()
                .html('<p>'+message+'</p>')
                .fadeIn()
                .delay(3000)
                .fadeOut('slow', function(){
                    $(this).empty();
                });
        } else {
            switch (message.type) {
                case 'confirm':
                    var control = '<br><p align="center"><a class="messageButtons" rel="true">si</a>&nbsp;&nbsp;&nbsp;<a class="messageButtons" rel="false">annulla</a></p>';
                    break;

                case 'input':
                    if(message.hasOwnProperty('value')){
                        var control = '<br><input type="text" placeholder="digita qui" value="' + message.value + '" class="messageInput"/></p><br>';
                    } else {
                        var control = '<br><input type="text" placeholder="digita qui" class="messageInput"/></p><br>';
                    }
                    control += '<p align="center"><a class="messageButtons" rel="true">continua</a>&nbsp;&nbsp;&nbsp;<a class="messageButtons" rel="false">annulla</a>';
                    break;

                case 'textarea':
                    var control = '<br><textarea type="text" placeholder="digita qui" class="messageTextarea" style="height: 100px">';
                    if(message.hasOwnProperty('value')){
                        control+= message.value;
                    }
                    control += '</textarea></p><br><p align="center"><a class="messageButtons" rel="true">continua</a>&nbsp;&nbsp;&nbsp;<a class="messageButtons" rel="false">annulla</a>';
                    break;

                case 'options':
                    var control = '<br>';
                    var checked = false;
                    for(var i =0; i < message.options.length; i++){
                        option = message.options[i];
                        var label = option.label;
                        var value = option.value;
                        var checkedStr = ''
                        if(!checked){
                            checked = option.checked;
                            if(checked){
                                checkedStr = ' checked="checked"';
                            }
                        }
                        control += '<input type="radio" name="messageOptions" class="messageOption" value="' + value + '" id="' + value + 'Option"' + checkedStr + '><label for="' + value + 'Option">' + label + '</label><br />';
                    }
                    control += '</p><br><p align="center"><a class="messageButtons" rel="true">continua</a>&nbsp;&nbsp;&nbsp;<a class="messageButtons" rel="false">annulla</a>';
                    break;
            }
            $('.messageButtons[rel="true"]').die('click');
            $('.messageButtons[rel="false"]').die('click');
            $('.toolbarMessage','.multimediaToTag')
                .empty()
                .html('<p>'+message.message+control+'</p>')
                .fadeIn('fast');
            
            $('.messageButtons[rel="true"]').live('click', function(e){
                e.preventDefault();
                $('.toolbarMessage','.multimediaToTag').fadeOut('fast');
                if(message.hasOwnProperty('onSubmit')){
                    message.onSubmit();
                }
            })
            $('.messageButtons[rel="false"]').live('click', function(e){
                e.preventDefault();
                $('.toolbarMessage','.multimediaToTag').fadeOut('fast');
                if(message.hasOwnProperty('onCancel')){
                    message.onCancel();
                }
            })
        }
    };

    function focusMessage(){
        prevBackground = $('.toolbarMessage','.multimediaToTag').css('background-color');
        $('.toolbarMessage','.multimediaToTag')
            .css('background-color', 'white')
            .animate({
                'backgroundColor': prevBackground
            });
    };

    function globalToLocal(e){
        globalX = e.pageX;
        globalY = e.pageY;
        mediaOffset = $_mediaToTag.offset();
        var local = {};
        local.xPx = e.pageX - mediaOffset.left;
        local.yPx = e.pageY - mediaOffset.top;
        local.x = local.xPx * 100 / mediaWidth;
        local.y = local.yPx * 100 / mediaHeight;
        return local;
    };

    $('#saveBEObject').preBind('click', function(){
        var json = $.stringify(texts, ' ');
        $("textarea[name='data[body]']").text(json);
        $("textarea[name='data[body]']").val(json);
    })

    $(".multimediaToTag").disableSelection();

};

jQuery.fn.extend({
    disableSelection : function() {
        return this.each(function() {
            this.onselectstart = function() { return false; };
            this.unselectable = "on";
            jQuery(this).css('user-select', 'none');
            jQuery(this).css('-o-user-select', 'none');
            jQuery(this).css('-moz-user-select', 'none');
            jQuery(this).css('-khtml-user-select', 'none');
            jQuery(this).css('-webkit-user-select', 'none');
        });
    },
    preBind: function(type, data, fn) {
        return this.each(function () {
            var $this = $(this);

            $this.bind(type, data, fn);

            var currentBindings = $this.data('events')[type];
            if ($.isArray(currentBindings)) {
                currentBindings.unshift(currentBindings.pop());
            }
        });
    }
});

//IE 6,7
jQuery.extend({
    stringify: function stringify(obj,space) {
        if(typeof space === 'undefined'){
            space = '';
        }
        if ("JSON" in window) {
            return JSON.stringify(obj,null,space);
        }

        var t = typeof (obj);
        if (t != "object" || obj === null) {
            // simple data type
            if (t == "string") obj = '"' + obj + '"';

            return String(obj);
        } else {
            // recurse array or object
            var n, v, json = [], arr = (obj && obj.constructor == Array);

            for (n in obj) {
                v = obj[n];
                t = typeof(v);
                if (obj.hasOwnProperty(n)) {
                    if (t == "string") {
                        v = space + '"' + v + '"';
                    } else if (t == "object" && v !== null){
                        v = jQuery.stringify(v);
                    }

                    json.push((arr ? "" : space + '"' + n + '":') + String(v));
                }
            }

            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    }
});




