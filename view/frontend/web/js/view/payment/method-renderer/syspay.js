define(
    [
        'jquery',
        'knockout',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/quote',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'moment',
        'mage/translate',
        'SysPay_Payment/js/action/delete-card',
        'SysPay_Payment/js/action/get-cards',
        'SysPay_Payment/js/action/after-place-order-redirect',
        'SysPay_Payment/js/action/token-available'
    ],
    function ($, ko, Component, placeOrderAction, quote, cardNumberValidator, moment, $t, deleteCardAction, getCardsAction, afterPlaceOrderRedirect, tokenAvailableAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'SysPay_Payment/payment/syspay'
            },

            redirectAfterPlaceOrder: false,

            savedCardsTokens: [],
            isUseCcForm: true,
            isAbleToSaveCard: false,
            isSaveCardEnabled: false,
            isSaveCardChecked: false,
            selectedCard: null,
            creditCardOwner: '',
            isShowCardActions: false,

            isHostedPageFlow: false,
            isServerToServerFlow: false,

            isShowVerificationNumberField: false,

            isShowSaveCardCheckbox: false,


            initialize: function () {
                const self = this;
                self._super();

                self.isHostedPageFlow = self.getConfig().isHostedPageFlow;
                self.isServerToServerFlow = !self.isHostedPageFlow;

                self.isUseCcForm(self.isServerToServerFlow);


                self.isAbleToSaveCard(self.getConfig().isAbleToSaveCard);
                self.isSaveCardEnabled(self.getConfig().isSaveCardEnabled);
                self.creditCardOwner(quote.billingAddress().firstname + ' ' + quote.billingAddress().lastname);

                self.isShowVerificationNumberField(self.hasVerification());


                if (self.isAbleToSaveCard()) {

                    getCardsAction(self.savedCardsTokens, self.messageContainer);


                    self.selectedCard.subscribe(function (card) {
                        if (card) {
                            if (self.isServerToServerFlow) {
                                self.isUseCcForm(false);
                            }
                            self.isShowCardActions(true);
                            self.isShowSaveCardCheckbox(false);
                        } else {
                            if (self.isHostedPageFlow) {
                                self.isShowSaveCardCheckbox(true);
                            }
                            self.isShowCardActions(false);

                        }
                    });


                    if (self.isHostedPageFlow) {
                        self.isShowSaveCardCheckbox(true);
                    }

                    if (self.isServerToServerFlow) {
                        self.isUseCcForm.subscribe(function (isUseCcForm) {
                            if (isUseCcForm) {
                                self.isShowSaveCardCheckbox(true);
                            } else {
                                self.isShowSaveCardCheckbox(false);
                            }
                        });

                        self.savedCardsTokens.subscribe(function (tokens) {
                            if (self.isSaveCardEnabled() && tokens.length > 0) {
                                self.isUseCcForm(false);
                            }
                        })
                    }
                }
            },

            /**
             *
             * @returns {*}
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'savedCardsTokens',
                        'isUseCcForm',
                        'isAbleToSaveCard',
                        'isSaveCardEnabled',
                        'isSaveCardChecked',
                        'selectedCard',
                        'creditCardOwner',
                        'isShowCardActions',
                        'isShowVerificationNumberField',
                        'isShowSaveCardCheckbox'
                    ]);

                return this;
            },

            /**
             *
             * @returns {string}
             */
            getCode: function () {
                return 'syspay';
            },

            /** @inheritdoc */
            addNewCardClickHandler: function () {
                this.isUseCcForm(true);
                this.selectedCard(null);
            },

            /**
             *
             * @returns {*|jQuery}
             */
            getPlaceOrderDeferredObject: function () {
                const self = this, deferred = $.Deferred();

                if (self.isUseCcForm()) {
                    self.tokenizeCard(self.getData(), function (response) {
                        if (response.error === null) {
                            const data = {
                                'method': self.getCode(),
                                'additional_data': {
                                    'tmp_payment_token': response.token
                                }
                            };

                            if (self.isSaveCardChecked() && self.isSaveCardEnabled()) {
                                data['additional_data']['save_card'] = true;
                            }
                            const placeOrderDeferred = placeOrderAction(data, self.messageContainer);
                            $.when(placeOrderDeferred).then(function () {
                                deferred.resolve();
                            }).fail(function () {
                                deferred.reject();
                            });
                        } else {
                            self.messageContainer.addErrorMessage({
                                message: response.message
                            });
                            deferred.reject();
                        }
                    });
                } else if (self.selectedCard()) {
                    tokenAvailableAction(this.selectedCard().id, self.messageContainer)
                        .done(function (isValid) {
                            if (isValid) {
                                const data = {}
                                data['method'] = self.getCode();
                                data['additional_data'] = {};
                                data['additional_data']['saved_card_token_id'] = self.selectedCard().id;
                                data['additional_data']['cc_cid'] = self.creditCardVerificationNumber()

                                const placeOrderDeferred = placeOrderAction(data, self.messageContainer);
                                $.when(placeOrderDeferred).then(function () {
                                    deferred.resolve();
                                }).fail(function () {
                                    deferred.reject();
                                });
                            } else {
                                self.messageContainer.addErrorMessage({
                                    message: $t('The card is not available')
                                });
                                deferred.reject();
                            }
                        });
                } else if (self.isHostedPageFlow) {
                    return self.getHostedPagePlaceOrderDeferredObject();
                }

                return deferred.promise();
            },

            getHostedPagePlaceOrderDeferredObject: function () {
                const self = this, data = {}

                data['method'] = self.getCode();
                data['additional_data'] = {};

                if (this.selectedCard()) {
                    const deferred = $.Deferred();
                    tokenAvailableAction(this.selectedCard().id, self.messageContainer)
                        .done(function (isValid) {
                            if (isValid) {
                                data['additional_data']['saved_card_token_id'] = self.selectedCard().id;
                                data['additional_data']['cc_cid'] = self.creditCardVerificationNumber()
                            } else {
                                deleteCardAction(self.selectedCard().id, self.messageContainer)
                            }

                            const placeOrderDeferred = placeOrderAction(data, self.messageContainer);
                            $.when(placeOrderDeferred).then(function () {
                                deferred.resolve();
                            }).fail(function () {
                                deferred.reject();
                            });
                        })
                    return deferred.promise();
                } else {
                    if (self.isSaveCardChecked() && self.isSaveCardEnabled()) {
                        data['additional_data']['save_card'] = true;
                    }
                    return $.when(
                        placeOrderAction(data, self.messageContainer)
                    )
                }
            },

            afterPlaceOrder: function () {
                afterPlaceOrderRedirect.execute();
            },

            /**
             *
             * @param modal
             */
            deleteCardAction: function (modal) {
                const self = this;
                if (!self.selectedCard()) {
                    modal.closeModal();
                    return;
                }
                deleteCardAction(self.selectedCard().id, self.messageContainer)
                    .done(function () {
                        self.savedCardsTokens.remove(function (item) {
                            return item.id === self.selectedCard().id;
                        });
                        self.selectedCard(null);
                        if (self.savedCardsTokens().length === 0 && self.isServerToServerFlow) {
                            self.isUseCcForm(true);
                        }
                    })
                    .always(function () {
                        modal.closeModal();
                    });
            },

            /**
             *
             * @returns {*}
             */
            getData: function () {
                let data = this._super();
                data['additional_data']['cc_owner'] = this.creditCardOwner();
                return data;
            },

            /**
             *
             * @returns {boolean}
             */
            isActive: function () {
                return window.checkoutConfig.payment[this.getCode()].isActive && syspay !== undefined;
            },

            /**
             *
             * @returns {boolean}
             */
            validate: function () {
                const self = this;

                if (this.isUseCcForm()) {
                    const errors = this.cardFormValidate();
                    if (errors.length > 0) {
                        errors.forEach(function (error) {
                            self.messageContainer.addErrorMessage({message: error});
                        });
                        return false;
                    }
                } else {
                    if (self.isHostedPageFlow && !self.selectedCard()) {
                        return true;
                    }
                    const cvvValidation = self.propertyValidationRules().ccCvv.validate();
                    let isValid = true;
                    if (!cvvValidation.isValid) {
                        self.messageContainer.addErrorMessage({
                            message: cvvValidation.message
                        });
                        $(self.propertyValidationRules().ccCvv.selector).find('input, select').addClass('mage-error');
                        isValid = false;
                    }
                    if (!this.selectedCard()) {
                        $('.saved-card-list select').addClass('mage-error')
                        self.messageContainer.addErrorMessage({
                            message: $t('Please select a card')
                        });
                        isValid = false;

                    }
                    return isValid;
                }
                return true;
            },

            /**
             *
             * @returns {*[]}
             */
            cardFormValidate: function () {
                const self = this, errorMessages = [], rules = self.propertyValidationRules();

                $.each(rules, function (k, v) {
                    if (v.validate) {
                        const validation = v.validate();
                        if (!validation.isValid) {
                            $(v.selector).find('input, select').addClass('mage-error');
                            errorMessages.push(validation.message);
                        }
                    }
                })

                return errorMessages;
            },

            /**
             *
             * @returns {{ccCvv: {label, value: *}, ccExpDate: {label, value: Date, validate: (function(): {isValid: *, message: *})}, ccNumber: {label, value: *, validate: (function(): {isValid, message: *})}, ccExpMonth: {label, value: *}, ccExpYear: {label, value: *}, ccOwner: {label, value: *, validate: (function(): {isValid: boolean})}}}
             */
            propertyValidationRules: function () {
                const self = this;
                const validateCreditCardCvv = function () {
                    const result = self.hasVerification() ? !!this.prop() : true;
                    return {
                        'isValid': result,
                        'message': $t('Please enter a valid credit card verification number.')
                    };
                };

                const validateCreditCardExpDate = function () {
                    const result = moment(this.prop()).isAfter(moment());
                    return {
                        'isValid': result,
                        'message': $t('Please enter a valid credit card expiration date.')
                    };
                };

                const validateCreditCardNumber = function () {
                    const result = cardNumberValidator(this.prop());
                    return {
                        'isValid': !(!result.isPotentiallyValid && !result.isValid),
                        'message': $t('Please enter a valid credit card number.')
                    };
                };

                const validateCreditCardOwner = function () {
                    const pattern = /^[A-Za-z\s]+$/;
                    const isValid = pattern.test(this.prop());
                    const result = {'isValid': true};

                    if (!isValid) {
                        result.isValid = false;
                        result.message = $t('Please enter a valid card owner name. ex : John Doe');
                    }

                    return result;
                };

                return {
                    ccCvv: {
                        selector: $(`#payment_form_${this.getCode()} #${this.getCode()}_cc_type_cvv_div`),
                        label: $t('Credit Card Verification Number'),
                        prop: self.creditCardVerificationNumber,
                        validate: validateCreditCardCvv
                    },
                    ccExpDate: {
                        selector: $(`#payment_form_${this.getCode()} #${this.getCode()}_cc_type_exp_div`),
                        prop: ko.computed(function () {
                            const expMonth = self.creditCardExpMonth();
                            const expYear = self.creditCardExpYear();

                            if (!expMonth || !expYear) {
                                return null;
                            }

                            return moment(`${expYear}-${expMonth}-01`, 'YYYY-MM-DD').format('YYYY-MM-DD');
                        }),
                        label: $t('Expiration Date'),
                        validate: validateCreditCardExpDate
                    },
                    ccNumber: {
                        selector: $(`#payment_form_${this.getCode()} #${this.getCode()}_cc_number_div`),
                        label: $t('Credit Card Number'),
                        prop: self.creditCardNumber,
                        validate: validateCreditCardNumber
                    },
                    ccOwner: {
                        selector: $(`#payment_form_${this.getCode()} #${this.getCode()}_cc_owner_div`),
                        label: $t('Name On card'),
                        prop: self.creditCardOwner,
                        validate: validateCreditCardOwner
                    }
                };
            },

            /**
             *
             * @param data
             * @param callback
             */
            tokenizeCard: function (data, callback) {
                syspay.tokenizer.setPublicKey(this.getConfig()['publicKey']);
                if (this.getConfig()['isSandbox']) {
                    syspay.tokenizer.setBaseUrl("https://app-sandbox.syspay.com/api/v1/public/");
                }
                const cardData = {
                    number: data['additional_data']['cc_number'],
                    cardholder: data['additional_data']['cc_owner'],
                    exp_month: moment(data['additional_data']['cc_exp_month'], 'M').format('MM'),
                    exp_year: data['additional_data']['cc_exp_year'],
                    cvc: data['additional_data']['cc_cid']
                };
                syspay.tokenizer.tokenizeCard(cardData, callback);
            },

            /**
             * @returns void
             */
            afterRender: function () {
                $(document).on('change input', '#payment_form_' + this.getCode() + ' input,' +
                    ' #payment_form_' + this.getCode() + ' select', function () {
                    $(this).removeClass('mage-error');
                });

                $(document).on('click', '[data-trigger="card-delete"]', function (e) {
                    e.preventDefault();
                });
            },

            /**
             *
             * @returns {*}
             */
            getConfig: function () {
                return window.checkoutConfig.payment[this.getCode()];
            },

            /**
             * @returns void
             * @param select
             */
            afterRenderSavedCards: function (select) {
                if (this.isHostedPageFlow) {
                    $(select)
                        .append($('<option>').val(null).text($t('Use a new one')));
                }
            },

            /**
             * @override
             *
             * @returns {*|boolean}
             */
            hasVerification: function () {
                if (this.isHostedPageFlow) {
                    return false;
                }
                return this._super();
            }
        });
    }
);
