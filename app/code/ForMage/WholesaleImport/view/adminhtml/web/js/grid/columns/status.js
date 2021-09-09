define([
        'Magento_Ui/js/grid/columns/select',
        'mage/translate'
    ], function (Column, $t) {
        'use strict';

        return Column.extend({
            defaults: {
                bodyTmpl: 'ui/grid/cells/html'
            },
            getLabel: function (record) {
                if (!record.status) {
                    return '';
                }

                var columnVal = record.status;

                if (columnVal === 'pending') {
                    return '<span class="grid-severity-notice" style="background:#fffbbb; color:#f38a5e; border-color: #f38a5e"><span>' + $t('Pending') + '</span></span>';
                } else if (columnVal === 'error') {
                    return '<span  class="grid-severity-critical"><span>' + $t('Error') + '</span></span>';
                } else  if (columnVal === 'complete'){
                    return '<span class="grid-severity-notice"><span>' + $t('Complete') + '</span></span>';
                } else  if (columnVal === 'canceled'){
                    return '<span class="grid-severity-minor"><span>' + $t('Canceled') + '</span></span>';
                }

                return '<span class="grid-severity-notice"><span>' + $t(columnVal) + '</span></span>';
            }
        });
    }
);

