jQuery(document).ready(function($) {

    initGrid();

    function initGrid() {

        var grid = new Muuri('.zrdn-grid', {
            dragEnabled: true,
            dragStartPredicate: function(item, e) {
                return e.target.className === 'zrdn-grid-title';
            },
            dragSortHeuristics: {
                sortInterval: 50,
                minDragDistance: 10,
                minBounceBackAngle: 1
            },
            dragPlaceholder: {
                enabled: false,
                duration: 400,
                createElement: function (item) {
                    return item.getElement().cloneNode(true);
                }
            },
            dragReleaseDuration: 400,
            dragReleseEasing: 'ease',
            layoutOnInit: true,
            // itemDraggingClass: 'muuri-item-dragging',
        })
        .on('move', function () {
            saveLayout(grid);
        });

        var layout = window.localStorage.getItem('layout');
        if (layout) {
            loadLayout(grid, layout);
        } else {
            grid.layout(true);
        }
        // Must save the layout on first load, otherwise filtering the grid won't work on a new install.
        saveLayout(grid);
    }

    function serializeLayout(grid) {
        var itemIds = grid.getItems().map(function (item) {
            return item.getElement().getAttribute('data-id');
        });
        return JSON.stringify(itemIds);
    }

    function saveLayout(grid) {
        var layout = serializeLayout(grid);
        window.localStorage.setItem('layout', layout);
    }

    function loadLayout(grid, serializedLayout) {
        var layout = JSON.parse(serializedLayout);
        var currentItems = grid.getItems();
        // // Add or remove the muuri-active class for each checkbox. Class is used in filtering.
        $('.zrdn-item').each(function(){
            var toggle_id = $(this).data('id');
            if (localStorage.getItem("toggle_data_id_"+toggle_id) === null) {
                window.localStorage.setItem('toggle_data_id_'+toggle_id, 'checked');
            }

            // // Add or remove the active class when the checkbox is checked/unchecked
            if (window.localStorage.getItem('toggle_data_id_'+toggle_id) == 'checked') {
                $(this).addClass("muuri-active");
            } else {
                $(this).removeClass("muuri-active");
            }
        });


        var currentItemIds = currentItems.map(function (item) {
            return item.getElement().getAttribute('data-id')
        });
        var newItems = [];
        var itemId;
        var itemIndex;

        for (var i = 0; i < layout.length; i++) {
            itemId = layout[i];
            itemIndex = currentItemIds.indexOf(itemId);
            if (itemIndex > -1) {
                newItems.push(currentItems[itemIndex])
            }
        }

        // Sort and filter the grid
        grid.sort(newItems, {layout: 'instant'});
        grid.filter('.muuri-active');
    }


    // Reload the grid when checkbox value changes
    $('.zrdn-item').each(function(){
        var toggle_id = $(this).data('id');
        // Set defaults for localstorage checkboxes
        if (!window.localStorage.getItem('toggle_data_id_'+toggle_id)) {
            window.localStorage.setItem('toggle_data_id_'+toggle_id, 'checked');
        }
        $('#toggle_data_id_'+toggle_id).change(function() {
            if (document.getElementById("toggle_data_id_"+toggle_id).checked ) {
                window.localStorage.setItem('toggle_data_id_'+toggle_id, 'checked');
            } else {
                window.localStorage.setItem('toggle_data_id_'+toggle_id, 'unchecked');
            }
            initGrid();
        });
    });

    /**
     * Show/hide dashboard items
     */

        //Get the window hash for redirect to #settings after settings save
    var tab = window.location.hash.substr(1).replace('#top','');
    $('ul.tabs li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });
    var href = $('.tab-'+tab).attr('href');
    if (typeof href !== 'undefined'){
        if (href.indexOf('#'+tab) !== -1 ) {
            $('.tab-'+tab)[0].click();
            window.location.href = href; //causes the browser to refresh and load the requested url
        }
    }


    /**
     * Checkboxes
     */

        // Get grid toggle checkbox values
    var formValues = JSON.parse(localStorage.getItem('formValues')) || {};
    var $checkboxes = $("#zrdn-toggle-dashboard :checkbox");

    // Enable all checkboxes by default to show all grid items. Set localstorage val when set so it only runs once.
    if (localStorage.getItem("zrdnDashboardDefaultsSet") === null) {
        $checkboxes.each(function () {
            formValues[this.id] = 'checked';
        });
        localStorage.setItem("formValues", JSON.stringify(formValues));
        localStorage.setItem('zrdnDashboardDefaultsSet', 'set');
    }

    // Update storage checkbox value when checkbox value changes
    $checkboxes.on("change", function(){
        updateStorage();
    });

    function updateStorage(){
        $checkboxes.each(function(){
            formValues[this.id] = this.checked;
        });
        localStorage.setItem("formValues", JSON.stringify(formValues));
    }

    // Get checkbox values on pageload
    $.each(formValues, function(key, value) {
        $("#" + key).prop('checked', value);
    });

    // Hide screen options by default
    $("#zrdn-toggle-dashboard").hide();

    // Show/hide screen options on toggle click
    $('#zrdn-show-toggles').click(function(){
        if ($("#zrdn-toggle-dashboard").is(":visible") ){
            $("#zrdn-toggle-dashboard").slideUp();
            $("#zrdn-toggle-arrows").attr('class', 'dashicons dashicons-arrow-down-alt2');
        } else {
            $("#zrdn-toggle-dashboard").slideDown();
            $("#zrdn-toggle-arrows").attr('class', 'dashicons dashicons-arrow-up-alt2');
        }
    });
});
