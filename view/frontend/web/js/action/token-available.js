define(
    [
        'mage/storage',
        'mage/url'
    ], function (storage, url) {
        'use strict';

        /**
         * @param {String} id
         * @param {Object} messageContainer
         */
        return function (id, messageContainer) {
            const serviceUrl = url.build('rest/V1/mine/syspay/token/available/' + id);
            return storage.get(
                serviceUrl, false
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
