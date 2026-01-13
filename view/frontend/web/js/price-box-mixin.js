define([
    'jquery',
    'mage/template',
    'text!Hmh_ProductSalesStats/template/sales-stats-message.html'
], function ($, mageTemplate, messageTemplate) {
    'use strict';

    return function () {
        $.widget('mage.priceBox', $.mage.priceBox, {
            _create: function () {
                this._super();
                this._renderSalesStats();
            },

            reloadPrice: function () {
                this._super();
                this._renderSalesStats(true);
            },

            _renderSalesStats: function (force) {
                if (!force && this._salesStatsInitialized) {
                    return;
                }

                this._salesStatsInitialized = true;

                const config = this.options.priceConfig;
                const stats = config && config.total_sold ? config.total_sold : null;

                if (!stats || !stats.qty || !stats.period) {
                    this._clearSalesStats();
                    return;
                }

                this._showSalesStats(stats);
            },

            _showSalesStats: function (stats) {
                this._clearSalesStats();

                const message = $('<div/>').appendTo(this.element);
                this._salesStatsElement = message;

                const messageText = $.mage.__('%1 sold in past %2')
                    .replace('%1', stats.qty)
                    .replace('%2', this._formatPeriod(stats.period));

                message.html(this._renderMessage(messageText));
            },

            _formatPeriod: function (period) {
                switch (period) {
                    case 'day':
                        return $.mage.__('day');
                    case 'week':
                        return $.mage.__('week');
                    case 'month':
                        return $.mage.__('month');
                    default:
                        return period;
                }
            },

            _renderMessage: function (messageText) {
                if (!this._messageTemplate) {
                    this._messageTemplate = mageTemplate(messageTemplate);
                }

                return this._messageTemplate({
                    message: messageText
                });
            },

            _destroy: function () {
                this._clearSalesStats();
                this._super();
            },

            _clearSalesStats: function () {
                if (this._salesStatsElement) {
                    this._salesStatsElement.remove();
                    this._salesStatsElement = null;
                }

                this._salesStatsInitialized = false;
            }
        });

        return $.mage.priceBox;
    };
});
