define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Training_Feedback/form/element/ratings',
            ratingOptions: [],
            ratingsData: {}
        },

        initialize: function () {
            this._super();
            this.ratingOptions = ko.observableArray(this.ratingOptions);
            this.ratingsData = ko.observable(this.ratingsData);
            return this;
        }
    });
});
