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
    fixItemsPriority: function() {
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
            $_this.show();
            if (pause != -1) {
                setTimeout(function() {
                    $_this.fadeOut(1000);
                });
            }               
        } else if (type == "warn") {
            $_this.show();
        }

        $_this.find(".closemessage").click(function() {
            $_this.hide();
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

            if (!$(this).hasClass("bedita")) {
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

   current module box fixed

...........................................*/

    if ($('.secondacolonna.fixed').length>0) {
        var fixedColumn = $('.secondacolonna.fixed');
        $(window).scroll(function(){
            var s = $(window).scrollTop();
            if (s<0) {
                s = 0;
            }
            fixedColumn.css('margin-top', s);
        });
    }

/*...........................................

   gray TABs

...........................................*/

    var currentclassmodule = BEDITA.currentModule.name || '';

    jQuery.fn.BEtabstoggle = function() {
        $(this).toggleClass("open").next().slideToggle('fast', function() { $(window).trigger('resize') }).toggleClass('open').trigger('slideToggle');
        $("h2", this).toggleClass("open").toggleClass(BEDITA.currentModule.name || '');

    };

    jQuery.fn.BEtabsopen = function() {

        $(this).addClass("open").next().slideDown('fast', function() { $(window).trigger('resize') }).addClass('open').trigger('slideDown');
        $("h2",this).addClass("open").addClass(BEDITA.currentModule.name || '');

    };

    jQuery.fn.BEtabsclose = function() {

        $(this).removeClass("open").next().slideUp('fast', function() { $(window).trigger('resize') }).removeClass('open').trigger('slideUp');
        $("h2",this).removeClass("open").removeClass(BEDITA.currentModule.name || '');

    };

    $(document).on('click', '.tab:not(.stayopen), .trigger', function () {
        $(this).BEtabstoggle();
    });


    $(".tab.stayopen H2").addClass("open").addClass(BEDITA.currentModule.name || '');


/*...........................................

   horizontal TABs

...........................................*/

    $(".htabcontainer .htabcontent:first-child").show();
    $(".htab TD:first-child,.htab LI:first-child").addClass("on");

    $(".htab TD, .htab LI").click(function() {

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

    //thead fix
    $(".indexlist tbody th").parent('TR').each(function() {
        if ($(this).closest('table').find('thead').length == 0) {
            var thead = $('<thead>');
            $(this).closest('table').prepend(thead);
            thead.append($(this));
        }
    });

    $(window).bind('resize', function() {
        $('.indexlist.js-header-float').each(function() {
            $(this).trigger('reflow');
        });
    });

    $('.indexlist.js-header-float').each(function() {
        $(this)
            .width( $(this).closest('.mainfull, .main').outerWidth() )
            .floatThead();
    });

    $(".indexlist TR").mouseover(function() {

        $("TD",this).addClass("over");

    }).mouseout(function(){

        $("TD",this).removeClass("over");

    });



    $(".indexlist TR:has(input.objectCheck:checked)").addClass("overChecked");

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

    jQuery.fn.BEmodal = function(options) {

        // default options for modal
        var defaultOptions = {
            title: '',
            destination: '',
            requestData: {},
            success: function() {},
        };

        if (typeof options == 'undefined' || !$.isPlainObject(options)) {
            options = {};
        }

        var options = $.extend({}, defaultOptions, options);

        var w = window.innerWidth || self.innerWidth || document.body.clientWidth;
        var h = window.innerHeight || self.innerHeight || document.body.clientHeight;

        var h = 1500;

        var destination = options.destination || $(this).attr("rel");
        var title = options.title || $(this).attr("title");

        var myTop = $(window).scrollTop() + 20;
        $("#modaloverlay").show().fadeTo("fast", 0.8);
        $("#modal #modalmain").show();
        $("#modal").toggle()/*.css("top", myTop)*/;

        if (destination) {
            $("#modal #modalmain").empty().append('<div class="loader"></div>');
            $("#modal #modalmain").find('.loader').show();
            $("#modalmain").load(destination, options.requestData, function(response, status, xhr) {
                $("#modal #modalmain").find('.loader').hide();
                options.success();
            });
        }

        if (title) {
            $("#modalheader .caption").html(title);
        }

        $("#modalheader .close").unbind('click').bind('click', function () {
            $("#modal").hide();
            $("#modaloverlay").hide();
            $(document).trigger('operation:cancel');
        });

        $("#modalheader .toggle").unbind('click').bind('click', function () {
            $("#modal #modalmain").toggle();
        });
    /*
        $("#modalheader .full").click(function () {

        });
    */
        //$("#modal").draggable();

        //prevent mousewheel propagation
        $(document).on('DOMMouseScroll mousewheel', '#modal .bodybg', function(ev) {
            var $this = $(this),
                scrollTop = this.scrollTop,
                scrollHeight = this.scrollHeight,
                height = $this.height(),
                delta = (ev.type == 'DOMMouseScroll' ?
                    ev.originalEvent.detail * -40 :
                    ev.originalEvent.wheelDelta),
                up = delta > 0;

            var prevent = function() {
                ev.stopPropagation();
                ev.preventDefault();
                ev.returnValue = false;
                return false;
            }

            if (!up && -delta > scrollHeight - height - scrollTop) {
                // Scrolling down, but this will take us past the bottom.
                $this.scrollTop(scrollHeight);
                return prevent();
            } else if (up && delta > scrollTop) {
                // Scrolling up, but this will take us past the top.
                $this.scrollTop(0);
                return prevent();
            }
        });

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
        form.find('select').each(function() {
            $(this).select2('val', $(this).val());
        });
        form.submit();
    });

/*...........................................

   autogrow

...........................................*/

    $('.autogrowarea').autosize();

/*...........................................

   bulk actions

...........................................*/

    $('.selecteditems').text($('.objectCheck:checked').length);

    $(document).on('click', '.selectAll', function() {
        var $this = $(this),
            status = $this.prop('checked'),
            ctx = $this.attr('data-context'),
            $allSelects = $((typeof ctx !== 'undefined') ? '[data-context="' + ctx + '"] .objectCheck' : '.objectCheck:not([data-context] .objectCheck)');

        $this.prop('indeterminate', false);
        $allSelects.each(function() {
            if ($(this).prop('checked') != status) {
                $(this).click();
            }
        });
    });

    $(document).on('click', '.objectCheck', function() {
        var ctx = $(this).closest('[data-context]').attr('data-context'),
            context = (typeof ctx !== 'undefined') ? ('[data-context="' + ctx + '"]') : ':not([data-context])',
            $allSelects = $((typeof ctx !== 'undefined') ? '[data-context="' + ctx + '"] .objectCheck' : '.objectCheck:not([data-context] .objectCheck)'),
            total = $allSelects.length,
            checked = $allSelects.filter(':checked').length;

        $('.selectAll' + context).prop('checked', checked == total).prop('indeterminate', checked > 0 && checked < total);
        $tab = $('.selecteditems' + context)
            .text(checked)
            .closest('.tab');
        if (checked) {
            $tab.BEtabsopen();
        } else {
            $tab.BEtabsclose();
        }
    });

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
        $(".searchobjectsbyid").css("visibility","hidden");
        $(".searchobjects").toggle();
    });

    $(".searchbyidtrigger").click(function() {
        $(".searchobjects").hide();
        var visibility = $(".searchobjectsbyid").css("visibility");
        if(visibility == "hidden") {
            $(".searchobjectsbyid").css("visibility","visible");
        } else {
            $(".searchobjectsbyid").css("visibility","hidden");
        }
    });

/*...........................................

   filter tab

...........................................*/

    $(".tab.filteractive").click();

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

   form enhancement / depend on libs/jquery.select2

...........................................*/

    $("select").not('.areaSectionAssociation, [name="filter[parent_id]"]').select2({
        dropdownAutoWidth:true
    }).on('select2-blur', function (e) {
        var select = this;
        for ( var i = 0, l = select.options.length, o; i < l; i++ ) {
            if (select.options[i].selected) {
                return true;
            }
        }
        $(this).select2('val', select.options[0].value);
    });

    $('select.areaSectionAssociation, [name="filter[parent_id]"]')
        .select2({
            escapeMarkup: function(m) {
                return $('<div/>').html(m).text();
            },
            formatResult: function(state) {
                // escape html tags
                state.text = $('<div/>').html(state.text).text();
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

/* end of document ready() */

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
                var ntree = $(data).slice(1);
                ntree.insertAfter(option);
                var select = option.closest('select');
                $('input.select2-input').val(option.first().text()).trigger('keyup-change');
            },
            error: function(er) {
                console.log(er);
            }
        });
    }
}

/*...........................................    

   A[download] links

...........................................*/

var a = document.createElement('a');
if (typeof a.download == 'undefined') {
    $('A[download]').remove();
}

/*...........................................

   keyboard binding

...........................................*/

$(document).on("keydown", function(e) {
    var keycode = e.which;
    if (keycode == 27) {
        
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

    }
})


/*...........................................

   objects list

...........................................*/

$(document).on('click', '.showmore', function() {
    var container = $(this).closest('.obj');
    $(this).toggleText('+', '-');
    //mette il modified a 1
    var mod = $('.mod', container).val();
    if (mod > 0) {
        $('.mod',container).val(0);
    }
    else {
        $('.mod',container).val(1);
    }
    //e mostra la textarea del titolo al posto del titolo
    container.toggleClass('show_more');
});


/*...........................................

   openAtStart

...........................................*/

function openAtStart(openTabs) {
    var title = 'tabs.' + BEDITA.currentModule.name + '.' + BEDITA.action,
        openTabs = ('localStorage' in window && window['localStorage'] !== null) ? (localStorage.getItem(title) || openTabs) : openTabs;

    openTabs = openTabs.split(',');
    for (var i = 0; i < openTabs.length; i++) {
        // avoid bad id selector
        var tabId = openTabs[i];
        if (tabId != '#' && tabId.length > 1) {
            $(tabId).prev('.tab, .tab2').click();
        }
    }

    if (!('localStorage' in window) || window['localStorage'] === null) {
        console.warn('LocalStorage disabled!');
        return;
    }
    $(window).unload(function() {
        var openTabs = [];
        $('.tab, .tab2').each(function(i) {
            if (!$(this).next().is(':visible')) {
                return;
            }

            var idAttr = $(this).next().attr('id');
            if (idAttr != '' && typeof idAttr !== 'undefined') {
                openTabs.push('#' + idAttr);
            }
        });

        localStorage.setItem(title, openTabs);
    });
}

/*...........................................

   utility

...........................................*/

function capitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


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

function addCsrfToken(postData, searchIn) {
    if (!postData) {
        postData = {};
    }
    if (!postData.data) {
        postData.data = {};
    }
    $item = (searchIn) ? $(searchIn) : $('body');
    var csrfToken = $item.find('input[name=data\\[_csrfToken\\]\\[key\\]]:first').val();
    if (csrfToken) {
        postData.data._csrfToken = {key: csrfToken};
    }
    return postData;
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
(function(window, $) {

	function ListHandler(list) {

		this.list = [];

		return this;
	}

	/**
	 * add or remove items from list
	 *
	 * @param  mixed items
	 * @param  string operation Can be 'add' or 'delete'
	 */
	ListHandler.prototype.handleList = function(items, operation) {
		if (typeof items != 'undefined') {
			if (!Array.isArray(items)) {
				items = [items];
			}
			for (var i in items) {
				if (!$.isPlainObject(items[i]) && !Array.isArray(items[i])) {
					var index = $.inArray(items[i], this.list);
					if (operation == 'add') {
						if (index == -1) {
							this.list.push(items[i]);
						}
					} else if (operation == 'remove') {
						if (index != -1) {
							this.list.splice(index, 1);
						}
					}
				}
			}
		}
		return this.list;
	};

	/**
	 * add items to list
	 * items already in list will not added
	 * @param mixed items
	 */
	ListHandler.prototype.add = function(items) {
		return this.handleList(items, 'add');
	};

	/**
	 * remove object's ids
	 * @param  mixed items
	 */
	ListHandler.prototype.remove = function(items) {
		return this.handleList(items, 'remove');
	};

	/**
	 * return the list
	 * @return Array
	 */
	ListHandler.prototype.get = function() {
		return this.list;
	};

	// expose constructor to window
	window.ListHandler = ListHandler;

})(window, jQuery);
