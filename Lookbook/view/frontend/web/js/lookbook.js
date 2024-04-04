define([
    'jquery',
    'easing',
    'easypin',
    'magiccart/gridtab',
    'jquery-ui-modules/widget',
], function($, easing, easypinShow, gridtab) {
    'use strict';

    $.widget('magiccart.lookbook', {
        options: {
            lookbookSelector: '.magic-pin-banner-wrap'
        },

        _create: function() {
            this._initLookbook();
            this._showLookBook();
        },

        _initLookbook: function() {
            var options = this.options;
            var self = this;
            self.element.find(options.lookbookSelector).each(function() {
                var _this = $(this);
                if(!$(_this).hasClass('magic-inited')) {
                    $(_this).addClass('magic-inited');
                    var _dataJson = _this.find('.json-data-pin'),
                        _init    = _dataJson.length ? _dataJson.text() : $(_this).data('pin'),
                        _img     = $(_this).find('img.magic_pin_image, img.magic_pin_pb_image'),
                        _tpl     = $(_this).find('.magic-easypin-tpl');
                    $(_this).find('popover a').each(function() {
                        $(this).attr('href', decodeURI($(this).attr('href')));
                    });
                    if(_init && $(_img).length >0) {
                        _img.attr('easypin-id', _img.data('easypin-id'));
                        _tpl.attr('easypin-tpl', '');
                        $(_img).easypinShow({
                            data: _init,
                            responsive: true,
                            popover: { show: false, animate: false },
                            each: function(index, data) {
                                return data;
                            },
                            error: function(e) {
                                console.log(e);  
                            },
                            success: function() {
                            }
                        });
                    }
                    
                    $(_img).click(function() {
                        $(_this).find('.easypin-popover').hide();
                    });
                    
                    $(document).on('keyup', function(e){
                        if (e.keyCode === 27) $(_img).click();
                    });
                }
            });
        },
        _showLookBook: function() {
            var lookbookList = $('.lookbook-list');
            if (!lookbookList.hasClass('gridtab')) {
                lookbookList.gridtab(lookbookList.data());
            }
        }

    });
    return $.magiccart.lookbook;
});