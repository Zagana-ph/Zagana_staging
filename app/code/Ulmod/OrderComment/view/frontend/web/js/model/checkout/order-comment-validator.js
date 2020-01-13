define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messageList',
        'mage/translate'		
    ],
	function ($, customer, quote, urlBuilder, urlFormatter, errorProcessor, messageContainer, __) {			
        'use strict';

        return {

            /**
             * Make an ajax PUT request to save order comment in the quote.
             *
             * @returns {Boolean}
             */
            validate: function () {
                var isCustomer = customer.isLoggedIn();
                var form = $('.payment-method input[name="payment[method]"]:checked').parents('.payment-method').find('form.order-comment-form');

                var quoteId = quote.getQuoteId();
                var url;
				
                // validate max length
                var comment = form.find('.input-text.order-comment').val();
                if (this.hasMaxCommentLength() && comment.length > this.getMaxCommentLength()) {
                    messageContainer.addErrorMessage({ message: __("The order comment entered exceeded the limit") });
                    return false;
                }
				
                if (isCustomer) {
                    url = urlBuilder.createUrl('/carts/mine/set-order-comment', {})
                } else {
                    url = urlBuilder.createUrl('/guest-carts/:cartId/set-order-comment', {cartId: quoteId});
                }

                var payload = {
                    cartId: quoteId,
                    orderComment: {
                        comment: comment
                    }
                };

                if (!payload.orderComment.comment) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: false
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );

                return result;
            },	
	
            /**
             * Is order comment has max length
             *
             * @return {Boolean}
             */			
            hasMaxCommentLength: function() {
                 return window.checkoutConfig.max_length > 0;
            },
			
            /**
             * Retrieve order comment length limit
             *
             * @return {String}
             */			
            getMaxCommentLength: function () {
                 return window.checkoutConfig.max_length;
            }		
        };
    }
);