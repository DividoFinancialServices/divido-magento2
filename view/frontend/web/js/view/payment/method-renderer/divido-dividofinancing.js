define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Divido_DividoFinancing/js/action/set-payment-method',
        'Divido_DividoFinancing/js/model/credit-request',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, Component, quote, setPaymentMethodAction, creditRequest, errorProcessor, fullScreenLoader) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Divido_DividoFinancing/payment/form',
                transactionResult: '',
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'transactionResult'
                    ]);

                return this;
            },

            getCode: function () {
                return 'divido_financing';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                }
            },

            getTransactionResults: function () {
                return _.map(window.checkoutConfig.payment.divido_financing.transactionResults, function (value, key) {
                    return {
                        'value':              key,
                        'transaction_result': value
                    }
                });
            },

            continueToDivido: function () {
                fullScreenLoader.startLoader();

                var email   = $('#customer-email').val();
                var planId  = $('select[name=divido_finance]').val();
                var deposit = $('input[name=divido_deposit]').val();

                var setPayment = setPaymentMethodAction(this.messageContainer)
                    .done(function () {
                        creditRequest(planId, deposit, email)
                            .done(function (data) {
                                fullScreenLoader.stopLoader();
                                window.location.replace(data[0]);
                            })
                            .fail(function (response) {
                                fullScreenLoader.stopLoader();
                                errorProcessor.process(response, this.messageContainer);
                            });
                    })
                    .fail(function (response) {
                        errorProcessor.process(response, this.messageContainer);
                        fullScreenLoader.stopLoader();
                    });
            }
        });
    }
);
