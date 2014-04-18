$(document).ready(function() {
	var onTreeBranchOpen = function() {
        $('.publishingtree .pub > ul.menutree').not('.branch, :empty').each(function() {
            $t = $(this);
            $t.addClass('branch').find('ul').each(function() {
                $(this).parent().prepend('<div class="hitarea">');
            });

            var on = $t.closest('.pub').find('.on, :checked');
            if (on.length != 0) {
                on
                    .addClass(BEDITA.currentModule.name)
                    .parents('ul')
                    .show()
                    .parents('li')
                    .addClass('collapsable');

                on
                    .filter(':checked')
                    .closest('li')
                    .children('A')
                    .addClass(BEDITA.currentModule.name);
                    
                if (on.parent().parent().hasClass('pub')) {
                    on
                        .parent()
                        .addClass('on')
                        .addClass(BEDITA.currentModule.name);
                } else {
                    $(this)
                        .closest('.pub')
                        .children('h2')
                        .addClass('open');
                }
            }

        });
    }

    $(document).delegate('.publishingtree .pub h2', 'click', function () {
        var $t = $(this);
        var ul = $t.siblings("ul.menutree").first();
        if (ul.is(':empty')) {
            var url = ul.attr('rel');
            $.ajax({
                url: url,
                success: function(data) {
                    var tree = $(data).find('ul.menutree').first();
                    ul.append( tree.html() );

                    onTreeBranchOpen();

                    ul.slideToggle(250, function() {
                        ul.find("input:checked").parent().css("background-color","#dedede").parents("ul, li").show();
                    });

                    $t.addClass("open");
                },
                error: function(er) {
                    console.log(er);
                }
            });
        } else {
            ul.slideToggle(250);
            $t.toggleClass("open");
        }
    }).delegate('.publishingtree A', 'click', function (ev) {
        ev.stopPropagation();
        var $t = $(this);
        var rel = $t.attr("rel");
        if (rel) {
            var ul = $t.closest('.pub').children("ul.menutree").first();
            var url = ul.attr('data-controller') + '/' + ul.attr('data-action') + '/id:' + rel;
            window.location = url;
        }
    }).delegate('.hitarea', 'click.publishingtree', function() {
        $(this)
            .parent()
            .toggleClass('collapsable')
            .children('ul')
            .slideToggle(250);
    });

    onTreeBranchOpen();
});