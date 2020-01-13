define(
    [
        'jquery',
        'uiComponent',
		'knockout'
    ],
    function ($, Component, ko) {
        'use strict';

		/**
		 * @param {Function} target
		 * @param {String} maxLength
		 * @return {*}
		 */
        ko.extenders.maxCommentLength = function (target, maxLength) {
            var timer;
			
            var result = ko.computed({
                read: target,
                write: function (val) {
                    if (maxLength > 0) {
                        clearTimeout(timer);
                        if (val.length > maxLength) {
                            var limitedVal = val.substring(0, maxLength);
                            if (target() === limitedVal) {
                                target.notifySubscribers();
                            } else {
                                target(limitedVal);
                            }
                            result.css("_error");
                            timer = setTimeout(function () { result.css(""); }, 800);
                        } else {
                            target(val);
                            result.css("");
                        }
                    } else {
                        target(val);
                    }
                }
            }).extend({ notify: 'always' });
			
            result.css = ko.observable();
            result(target());
			
            return result;
        };


        return Component.extend({
            defaults: {
                template: 'Ulmod_OrderComment/checkout/order-comment-block'
            },

            initialize: function() {
                this._super();
                var self = this;
				this.comment = ko.observable("").extend(
					{
						maxCommentLength: this.getMaxCommentLength()
					}
				);
                this.remainingCharacters = ko.computed(function(){
                    return self.getMaxCommentLength() - self.comment().length;
                });
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
        });
    }
);
