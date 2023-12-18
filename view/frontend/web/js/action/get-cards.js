define(
    [
        'mage/storage',
        'mage/url',
        'mage/translate'
    ], function (storage, url, $t) {
        'use strict';

        /**
         * @param {Object} cards
         * @param {Object} messageContainer
         */
        return function (cards, messageContainer) {
            const serviceUrl = url.build('rest/V1/mine/syspay/token/');
            return storage.get(
                serviceUrl, false
            )
                .done(
                    function (response) {
                        if (response && response.items) {
                            cards(response.items);
                        }
                    }
                )
                .fail(
                    function (response) {
                        if (response) {
                            messageContainer.addErrorMessage({
                                'message': response.responseJSON.message
                            });
                        }
                    }
                );
        }
    });
