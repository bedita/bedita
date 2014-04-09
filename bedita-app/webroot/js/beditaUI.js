/**
 * BeditaUI - javascript UI functions and methods 
 * @param {Object} ".modules *[rel]"
 */


/*...........................................    

   General functions

...........................................*/


/*
* Date - force two date pickers to work as a date range 
*/

function customRange(input)
{
    return {minDate: (input.id == 'end' ? $('#start').datepicker( "getDate" ) : null), 
            maxDate: (input.id == 'start' ? $('#end').datepicker( "getDate" )  : null)}; 
}


/*
 * Extend JQuery
 */
 
 /*...........................................    

   Base addon functions

...........................................*/   


jQuery.fn.toggleText = function(a, b) {
    return this.each(function() {
        jQuery(this).text(jQuery(this).text() == a ? b : a);
    });
};

jQuery.fn.toggleValue = function(a, b) {
    return this.each(function() {
        jQuery(this).val(jQuery(this).val() == a ? b : a);
    });
};



jQuery.fn.extend({
    check: function() {
        return this.each(function() { this.checked = true; });
    },
    uncheck: function() {
        return this.each(function() { this.checked = false; });
    },
    toggleCheck: function() {
        return this.each(function() { this.checked = !this.checked ; });
    },
    submitConfirm: function(params) {
        $(this).bind("click", function() {
            if(!confirm(params.message)) {
                return false ;
            }
            if (params.formId) {
                $("#" + params.formId).attr("action", params.action);
                $("#" + params.formId).submit(); 
            } else {
                $(this).parents("form").attr("action", params.action);
                $(this).parents("form").submit();
            }
        });
    },


/*
*   fixItemsPriority (was reorderListItems)
*   optional first parameter define priority start number
*/
    fixItemsPriority: function ()
    {
        if(window.priorityOrder === undefined) {
            priorityOrder = "asc";
        }
                    
        if(priorityOrder == "desc") {

            priority = parseInt( (arguments.length > 0 && typeof(arguments[0]) != 'object' && typeof(arguments[0]) != 'undefined')? arguments[0] : $(this).find("input[name*='[priority]']:first").val() );
            
            $(this).find("input[name*='[priority]']:enabled").each(function(index)
            {
                $(this).val(priority--).trigger('change')                           // update priority
                .hide().fadeIn(100).fadeOut(100).fadeIn('fast');
            });
            
        } else {
            priority = parseInt( (arguments.length > 0 && typeof(arguments[0]) != 'object' && typeof(arguments[0]) != 'undefined')? arguments[0] : 1 );
            
            $(this).find("input[name*='[priority]']:enabled").each(function(index)
            {
                $(this).val(priority++).trigger('change')           // update priority
                .hide().fadeIn(100).fadeOut(100).fadeIn('fast');    // pulse effect
            });
        }
    },

    triggerMessage: function(type, pause) {
        var $_this = $(this);
        if (pause == undefined) {
            pause = 3000;
        }
        if (type == "error") {
            $_this.show();
        } else if (type == "info") {
            $_this
                .show()
                .animate({opacity: 1.0}, pause)
                .fadeOut(1000);
        } else if (type == "warn") {
            $_this.show();
        }
            
        $_this.find(".closemessage").click(function() {
            $_this.fadeOut('slow');
        });
        
        $_this.find(".messagedetail").click(function() {
            $(this).next().toggle();
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



$(document).ready(function(){


/*...........................................    

   home

...........................................*/

    //$(".pub H2",".hometree").wrap("<div class='tab' />"); 

    $("LI A[rel]",".hometree").css("cursor","pointer").click(function () {
        window.location = $(this).attr("rel");
    });
    
    //$(".publishingtree LI A.on",".home").parents("DIV:first").find("UL").show();

/*...........................................    

   primacolonna

...........................................*/

    $(".primacolonna").click(function () {
        $("BODY").toggleClass("leftmenuopen");
    });

    
/*...........................................    

   moduloni

...........................................*/   
    
    $(".modules *[rel]").css("cursor","pointer").mouseover(function () {

            $(this).css("background-repeat","repeat");

        }).mouseout(function(){

            $(this).css("background-repeat","repeat-x");

        }).click(function () {
            
            if ($(this).hasClass("bedita")) {
                //
            } else {
                window.location = $(this).attr("rel");
            }
    });

/*...........................................    

   menu TOP moduli

...........................................*/

    $(".modulesmenu a").mouseover(function (e) {        

        var rightpos = e.clientX;   

        $(this).addClass("over");

        $(".modulesmenucaption").toggle().css("left",rightpos - 100);

        $(".modulesmenucaption A").text($(this).attr("title"));

    }).mouseout(function(){

        $(this).removeClass("over");

        $(".modulesmenucaption").toggle();

        $(".modulesmenucaption A").text("be");      

    }).click(function () {
        if ($(this).attr("href")) {
            window.location = ($(this).attr("href"));
        }
    });
        

/*...........................................    

   gray TABs

...........................................*/

    var currentclassmodule   = $(".secondacolonna .modules LABEL").attr("class");
    if(!currentclassmodule) {
        currentclassmodule = "";
    }

    jQuery.fn.BEtabstoggle = function() {
        $(this).next().toggle('fast') ; 
        $("h2",this).toggleClass("open").toggleClass(currentclassmodule);
    
    };

    jQuery.fn.BEtabsopen = function() {
            
        $(this).next().show('fast') ;   
        $("h2",this).addClass("open").addClass(currentclassmodule);
    
    };

    jQuery.fn.BEtabsclose = function() {
            
        $(this).next().hide('fast') ;   
        $("h2",this).removeClass("open").removeClass(currentclassmodule);
    
    };
    
    $(document).on('click', '.tab:not(.stayopen)', function () {
        $(this).BEtabstoggle();
    });

    
    $(".tab.stayopen H2").addClass("open").addClass(currentclassmodule);

    
/*...........................................    

   horizontal TABs

...........................................*/
    
    $(".htabcontainer .htabcontent:first-child").show();
    $(".htab TD:first-child,.htab LI:first-child").addClass("on");
    
    $(".htab TD,.htab LI").click(function() {

        var trigged           = $(this).attr("rel");
        var containermenu     = $(this).parents(".htab");
        var containercontents = $("#"+trigged+"").parent().attr("id");

        $("#"+containercontents).children().hide();
        $("#"+trigged+"").show();

        $("TD,LI",containermenu).removeClass("on");
        $(this).addClass("on");
    
      });

/*...........................................    

   tabelle e stati dei TR e click

...........................................*/



    $(".indexlist TR").mouseover(function() {

        $("TD",this).addClass("over");

    }).mouseout(function(){

        $("TD",this).removeClass("over");   

    });



    $(".indexlist TR:has(input:checked)").addClass("overChecked");
    
    $(".indexlist input.objectCheck").change(function(){

        $(this).parents("TR").toggleClass("overChecked");

    });


    
    $(".indexlist TR[rel]").not('.idtrigger').click(function() {
        
        window.location = ($(this).attr("rel"));
    });

/*
    $(".indexlist THEAD TH").click(function() {
        
        $(this).toggleClass("on");  
        
    });
*/

/*...........................................    

   publishing tree

...........................................*/

    $(".menutree input:checked").parent().css("background-color","#dedede").parents("ul, li").show();
    $(".publishingtree h2 A").before("<a class='plusminus'></a>");
    
    $(".publishingtree h2 .plusminus").click(function () {
        var $t = $(this);
        var who = $t.parent("H2");
        var ul = $(who).siblings("ul.menutree").first();
        if (ul.is(':empty')) {
            var url = ul.attr('rel');
            $.ajax({
                url: url,
                success: function(data) {
                    var tree = $(data).find('ul.menutree').first();
                    ul.append( tree.html() );
                    ul.treeview({ 
                        animated: "normal",
                        collapsed: true,
                        unique: false
                    });

                    $("LI A", ul).each(function() {
                        if ($(this).attr("rel")) {
                            var rel = $(this).attr("rel");
                            rel = '/' + ul.attr('data-controller') + '/' + ul.attr('data-action') + '/id:' + rel;
                            $(this).attr("rel", rel);
                        }
                    }).click(function () {
                        if ($(this).attr("rel")) {
                            window.location = $(this).attr("rel");
                        }
                    });

                    ul.slideToggle(800, function() {
                        $(".menutree input:checked").parent().css("background-color","#dedede").parents("ul, li").show();
                    });
                    $(who).toggleClass("open");
                },
                error: function(er) {
                    console.log(er);
                }
            });
        } else {
            ul.slideToggle(800);
            $(who).toggleClass("open");
        }
    });

    $('.publishingtree .pub > ul.menutree').not(':empty').each(function() {
        $(this)
            .treeview({ 
                animated: "normal",
                collapsed: false,
                unique: false
            })
            .find('.on, :checked')
            .parents('ul')
            .show()
            .parents('li')
            .removeClass('expandable')
            .addClass('collapsable')
            .children('.hitarea')
            .removeClass('expandable-hitarea')
            .addClass('collapsable-hitarea')
            .closest('.pub')
            .children('h2')
            .addClass('open');

        var on = $(this).closest('.pub').find('.on, :checked');
        if (on.length != 0) {

            if (on.parent().parent().hasClass('pub')) {
                on.parent().addClass('on');
            } else {
                $(this)
                    .closest('.pub')
                    .children('h2')
                    .addClass('open');
            }
        }

    });

    $(".publishingtree A").each(function() {
        var $t = $(this);
        var ul = $t.closest('.pub').children("ul.menutree").first();
        if ($t.attr("rel")) {
            var rel = $t.attr("rel");
            rel = '/' + ul.attr('data-controller') + '/' + ul.attr('data-action') + '/id:' + rel;
            $t.attr("rel", rel);
        }
    });

    $(".publishingtree A").click(function () {
        if ($(this).attr("rel")) {
            window.location = $(this).attr("rel");
        }
    });

    $(".publishingtree .on").addClass(currentclassmodule);

/*...........................................    

   multimediaitem

...........................................*/   

    $("#viewthumb .multimediaitem").mouseover(function () {

        $(this).toggleClass("dark");

    }).mouseout(function () {
        
        $(this).toggleClass("dark");

    });


/*...........................................    

   multimediaitem icon toolbar

...........................................*/

    $(".multimediaitemToolbar.viewlist").click(function () {
        
        $(".vlist, .imagebox, .info_file_item").toggle();
        
        $('.multimediaitem').css("float","none");
        
        $('.vlist').css("white-space","nowrap");
    
    });
    
    $(".multimediaitemToolbar.viewsmall").click(function () {
        $("#viewlist").hide();
        $("#viewthumb").show();
        $(".multimediaitem").addClass("small");
    });

    $(".multimediaitemToolbar.viewthumb").click(function () {
        $("#viewlist").hide();
        $("#viewthumb").show();
        $(".multimediaitem").removeClass("small");
    });

    $("#mediatypes LI").click(function () {
    
        $("#mediatypes LI").removeClass("on");
        $(this).addClass("on");
        var valore = $("input", this).attr("value");
        $("#mediatypes input").val([valore]);
        if ($(this).attr("rel")) {
                window.location = $(this).attr("rel");
        }
    });

    $("#mediatypes LI.ico_all").click(function () {
        $("#mediatypes LI").addClass("on");
    });

/*...........................................    

   modal

...........................................*/

    jQuery.fn.BEmodal = function(){

        $("#modal").draggable({
            handle : "#modalheader"
        });

        var w = window.innerWidth || self.innerWidth || document.body.clientWidth;
        var h = window.innerHeight || self.innerHeight || document.body.clientHeight;
        
        var h = 1500; 
        //alert(h);
        
        var destination = $(this).attr("rel");
        var title = $(this).attr("title");
        
        var myTop = $(window).scrollTop() + 20;
        
        $("#modaloverlay").show().fadeTo("fast", 0.8).width(w).height(h).click(function () {
            //$(this).hide();
            //$("#modal").hide();
        });
        
        $("#modal").toggle().css("top",myTop);

        if ($(this).attr("rel")) {
            $("#modalmain").empty().addClass("modaloader").load(destination, function(response, status, xhr) {
                $(this).removeClass("modaloader");
            });
        };

        
        if ($(this).attr("title")) {
            $("#modalheader .caption").html(title+"&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;");
        };
        
        
        $("#modalheader .close").click(function () {
            $("#modal").hide();
            $("#modaloverlay").hide();
            $(document).trigger('operation:cancel');
        });
    /*
        $("#modalheader .full").click(function () {

        });
    */

    }

    $(".modalbutton").click(function () {
    
        $(this).BEmodal();

    });


/*...........................................    

   bottons

...........................................*/

    $(".BEbutton .golink").click(function () {
        if ($(this).attr("href")) {
            window.open($(this).attr("href"));
        }
    });

    $(document).on('click', '#cleanFilters', function(ev) {
        ev.preventDefault();
        var form = $(this).parents('form:first');
        form.find('input[name=cleanFilter]').val(1);
        form[0].reset();
        form.submit();
    });
        
/*...........................................    

   autogrow

...........................................*/
    
    $('.autogrowarea').autosize();
                        
/*...........................................    

   bulk actions

...........................................*/


    $('.selecteditems').text($(".objectCheck:checked").length);
    $(".selectAll").bind("click", function(e) {
        var status = this.checked;
        $(".objectCheck").each(function() { 
            this.checked = status; 
            if (this.checked) $(this).parents('TR').addClass('overChecked');
            else $(this).parents('TR').removeClass('overChecked');
        });
        $('.selecteditems').text($(".objectCheck:checked").length);
    }) ;
    $(".objectCheck").bind("click", function(e) {
        var status = true;
        $(".objectCheck").each(function() { 
            if (!this.checked) return status = false;
        });
        $(".selectAll").each(function() { this.checked = status;});
        $('.selecteditems').text($(".objectCheck:checked").length);
    }) ;

/*...........................................    

   help

...........................................*/



/*...........................................    

   editor notes

...........................................*/

    $('.editorheader').css("cursor","pointer").click(function () {
        $(this).next().slideToggle('fast');
    });


/*...........................................    

   accessories

...........................................*/

    $(".idtrigger").css("cursor","pointer").click(function() {
        var trigged  = $(this).attr("rel");
        $("#"+trigged+"").toggle();
    });

/*...........................................    

   search

...........................................*/

    $(".searchtrigger").click(function() {
        $(".searchobjects").toggle();
    });

/*...........................................    

   filter tab

...........................................*/
    $(".tab.filteractive").click();


/*...........................................    

   versin / history

...........................................*/


/*...........................................    

  modulelist menu

...........................................*/


    $('.primacolonna .modules label.bedita').click(function(e) {
        if ($('.modulesmenu_d').length) {
            if ($('.modulesmenu_d:visible').length) {
                $('.modulesmenu_d').hide();
            } else {
                $('.modulesmenu_d').show();
            }

        }
    });

    $(".modulesmenu_d LI[class]").each(function() {
        var classname = $(this).attr('class');
        var color = $(".modulesmenu ."+classname).css('background-color');
        $(this).css("border-color", color);
    });

    $(".modulesmenu_d LI[class]").hover(
      function () {
        var classname = $(this).attr("class");
        var position = $(this).position();
        var topshift = position.top;
        var leftshift = $(".modulesmenu_d").width();
        $(".sub_modulesmenu_d."+classname+"").css({
            "left": leftshift+160+"px",
            "top":  topshift+20+"px"
        }).show();
        
      }, 
      function () {
        $(".sub_modulesmenu_d").hide();
      }
    );


/*...........................................    

   statusInfo

...........................................*/

    //var objstatus = $(".secondacolonna .modules label").attr("class");

/*...........................................    

   form enhancement / depend on libs/jquery.bsmselect

...........................................*/

    //$('select[multiple]').chosen({width: '95%'});
    $("select").not('.areaSectionAssociation, [name="filter[parent_id]"]').select2({
        dropdownAutoWidth:true,
        allowClear: true
    });

    $('select.areaSectionAssociation, [name="filter[parent_id]"]')
        .select2({
            escapeMarkup: function(m) { return m; },
            formatResult: function(state) {
                if ($(state.element).is('.pubOption')) {
                    return '<a rel="'+$(state.element).attr('rel')+'" onmouseup="toggleSelectTree(event)">> </a>'+state.text;
                } else {
                    if (!$(state.element).is(':first-child')) {
                        var ar = state.text.split(' > ');
                        var last = ar.pop();
                        return '<span class="gray">'+ar.join(' > ')+' > </span>'+last;
                    } else {
                        return state.text;
                    }
                }
            }
        });

});

var toggleSelectTree = function(ev) {
    ev.preventDefault();
    ev.stopPropagation();
    var pubLi = $(ev.target).closest('.pubOption');
    var subLi = pubLi.nextUntil('.pubOption');
    var url = $(ev.target).attr('rel');
    var option = $('option[rel="'+url+'"]');
    if (subLi.length>0) {
        $('input.select2-input').val(option.first().text()).trigger('keyup-change');
    } else {
        $.ajax({
            url: url,
            success: function(data) {
                data = $.trim(data);
                var ntree = $(data).slice(2);
                ntree.insertAfter(option);
                var select = option.closest('select');
                console.log(select);
                $('input.select2-input').val(option.first().text()).trigger('keyup-change');
            },
            error: function(er) {
                console.log(er);
            }
        });
    }
}

/* end of document ready() */


/*...........................................    

   keyboard binding

...........................................*/

document.onkeydown = function(e){   
    if (e == null) { // ie
        keycode = event.keyCode;
    } else { // mozilla
        keycode = e.which;
    }
    
    if(keycode == 27){ // 
        
        if ($('.tab').next().is(":visible")) {
            $('.tab').BEtabsclose();
        } else {
            $('.tab').BEtabsopen();
        }

        if ($('.tab2').next().is(":visible")) {
            $('.tab2').BEtabsclose();
        } else {
            $('.tab2').BEtabsopen();
        }
        
    } else if(keycode == 109){ // 
        
        //$('.tab').BEtabsopen();
        //helptrigger
        
    } else if(keycode == 122){ // 
        
        //$('.helptrigger').click();
    
    
    } else if(keycode == 188){ // 
        
    }
    //alert(keycode);
};


/*...........................................    

   openAtStart

...........................................*/

function openAtStart(defaultOpen) {
    if ('localStorage' in window && window['localStorage'] !== null) {

        var title = "tabs." + BEDITA.currentModule.name + "." + BEDITA.action;
        var openAtStart = localStorage.getItem(title);
        
        if (openAtStart == null) {
            var openAtStart = defaultOpen;
        }
        var openTmp = openAtStart.split(',');
        for(var i=0; i < openTmp.length; i++) {
            // avoid bad id selector
            var tabId = openTmp[i];
            if(tabId != '#' && tabId.length > 1) {
                $(tabId).prev(".tab").BEtabstoggle();
            }
        }

        $(window).unload(function(){
            openAtStart = new Array();
            $(".tab").each(function(i){
                if ($(this).next().is(":visible")) {
                    idAttr = $(this).next().attr("id");
                    if(idAttr != "") {
                        openAtStart.push("#" + idAttr);
                    }
                }
            });

            localStorage.setItem(title, openAtStart);
        });

    }
}

/*...........................................    

   utility

...........................................*/
      
      
function getFlashVersion(){ 
    // ie 
    try { 
        try { 
            // avoid fp6 minor version lookup issues 
            // see: http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/ 
            var axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6'); 
            try { 
                axo.AllowScriptAccess = 'always'; 
            } catch(e) {
                return '6,0,0';
            } 
        } catch(e) {} 
        return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g, ',').match(/^,?(.+),?$/)[1]; 
    // other browsers 
    } catch(e) { 
        try { 
            if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin){ 
                return (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1]; 
            } 
        } catch(e) {} 
    } 

    return false; 
} 


/**
 * Handle a list of items
 * Items in list can be string, integer, boolean
 *
 * Example:
 *
 * var mylist = new ListHandler();
 * mylist.add(['one', 'two']);
 * mylist.add('three');
 * mylist.remove('one');
 * mylist.remove(['two', 'three'])
 *
 * @contructor
 */
function ListHandler() {

	/**
	 * @type Array
	 */
	var list = [];

	/**
	 * add or remove items from list
	 *
	 * @param  mixed items
	 * @param  string operation Can be 'add' or 'delete'
	 */
	var handleList = function(items, operation) {
		if (typeof items != 'undefined') {
			if (!Array.isArray(items)) {
				items = [items];
			}
			for (var i in items) {
				if (!$.isPlainObject(items[i]) && !Array.isArray(items[i])) {
					var index = $.inArray(items[i], list);
					if (operation == 'add') {
						if (index == -1) {
							list.push(items[i]);
						}
					} else if (operation == 'remove') {
						if (index != -1) {
							list.splice(index, 1);
						}
					}
				}
			}
		}
		return list;
	}

	/**
	 * add items to list
	 * items already in list will not added
	 * @param mixed items
	 */
	this.add = function(items) {
		return handleList(items, 'add');
	}

	/**
	 * remove object's ids
	 * @param  mixed items
	 */
	this.remove = function(items) {
		return handleList(items, 'remove');
	}

	/**
	 * return the list
	 * @return Array
	 */
	this.get = function() {
		return list;
	}
}
