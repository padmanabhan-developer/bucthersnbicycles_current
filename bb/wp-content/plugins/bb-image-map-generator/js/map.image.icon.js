MapImageIcon = {
    // Icon counter
    _mapIconCounter : 0,
    // Icon width
    iconW: 27,
    // Icon height
    iconH: 27,

    // Main function
    init: function() {
        var offsetHolder, imgHolder, iconX, iconY, that = this, iconCoordsRow;
        // Image container
        var imgHolder = jQuery('.map-image-holder');
        // Image elem itself
        var imgElem = imgHolder.find('img');

        // Image click handler
        imgElem.on('click', function(event) {
            // Offset for parent element (from left top corner)
            offsetHolder = imgHolder.offset();
            // Offset from left top corner to mouse cursor
            iconX = window.parseInt(event.pageX - offsetHolder.left - Math.round(that.iconW/2));
            // Offset in percentage
            iconXPr = (iconX/imgElem.width()*100).toFixed(3);
            iconY = window.parseInt(event.pageY - offsetHolder.top - Math.round(that.iconH/2));
            // Offset in percentage
            iconYPr = (iconY/imgElem.height()*100).toFixed(3);

            // Add icon on the image
            jQuery('<div class="map-image-icon"></div>')
                // Set id
                .attr('id', 'map-icon-' + (that._mapIconCounter))
                // Set position
                .css({top: iconYPr + '%', left: iconXPr  + '%'})
                .appendTo(imgHolder);

            // Add block with input elements holding icon coordinates
            jQuery('.map-points-holder').append(
                '<div>'+
                '<input type="text" name="iconCoords[' + that._mapIconCounter + '][x]" value=""/>'+
                '<input type="text" name="iconCoords[' + that._mapIconCounter + '][y]" value=""/>'+
                '<input type="text" name="iconCoords[' + that._mapIconCounter + '][text]" value=""/>'+
                '<input type="button" data-icon-id="' + that._mapIconCounter + '" value="-"/>'+
                '</div>');
            // Get last added icon row
            iconCoordsRow = jQuery('.map-points-holder div:last');
            // Get first input and put X value
            iconCoordsRow.find('input:eq(0)').val(iconXPr);
            // Get second input and put Y value
            iconCoordsRow.find('input:eq(1)').val(iconYPr);

            that._mapIconCounter++;
        })

        // Add handler for 'delete icon' button
        this._mapIconDelete();
        // Debug panel (to view coordinates)
        this._mouseMoveDebug();
    },

    _mapIconDelete: function() {
        var mapIconId;
        jQuery('.map-points-holder').on('click', 'div input:button', function() {
            // Get icon id from data attr
            mapIconId = jQuery(this).data('iconId');
            jQuery(this).parent().remove();
            jQuery('#map-icon-' + mapIconId).remove();
        })
    },

    _mouseMoveDebug: function() {
        var parentOffset, offsetX, offsetY;
        var imgElem = jQuery('.map-image-holder img');
        imgElem.on('mousemove', function(event) {
            parentOffset = jQuery(this).parent().offset();
            offsetX = window.parseInt(event.pageX - parentOffset.left);
            offsetXPr = (offsetX/imgElem.width()*100).toFixed(3);
            offsetY = window.parseInt(event.pageY - parentOffset.top);
            offsetYPr = (offsetY/imgElem.height()*100).toFixed(3);

            jQuery('.map-debug-holder')
                .html('<em>X: ' + offsetX + 'px | ' + offsetXPr + '%<br />Y: ' + offsetY + 'px | ' + offsetYPr + '%</em>');
        })
    },

    showIconSet: function(iconSet) {
        if (!window.iconSet) return false;

        // Image container
        var parent = jQuery('.map-image-holder');
        // Image element itself
        var imgElem = parent.find('img');

        for (var i in iconSet) {
            jQuery('<div class="map-image-icon"></div>')
                .addClass('tooltip')
                .attr('title', iconSet[i].text)
                .attr('id', 'map-icon-' + i)
                // Set position
                .css({top: iconSet[i].y + '%', left: iconSet[i].x  + '%'})
                .appendTo(parent);
        }
        // Add tooltip
        parent.find('.tooltip').tooltipster({contentAsHTML: true, interactive: true, theme: 'tooltipster-bb'});

        // Update icon counter
        this._initMapIconCounter();
    },

    _initMapIconCounter: function() {
        // Get amount of previously added icons
        var iconCount = jQuery('.map-image-holder .map-image-icon').length;
        this._mapIconCounter = iconCount > 0 ? iconCount : 0;
    }
};
