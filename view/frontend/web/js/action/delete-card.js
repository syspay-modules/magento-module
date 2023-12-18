define(
    [
        'mage/storage',
        'mage/url',
        'mage/translate'
    ], function (storage, url, $t) {
        'use strict';

        /**
         * @param {String} tokenId
         * @param {Object} messageContainer
         */
        return function (tokenId, messageContainer) {
            const serviceUrl = url.build('rest/V1/mine/syspay/token/' + tokenId);
            return storage.delete(
                serviceUrl, false
            ).done(
                function (response) {
                    if (response) {
                        messageContainer.addSuccessMessage({
                            'message': $t('Card deleted')
                        });
                    }
                }
            ).fail(
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
