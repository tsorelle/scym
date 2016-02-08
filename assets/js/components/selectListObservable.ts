/**
 * Created by Terry on 2/7/2016.
 */
/// <reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="../typings/knockout/knockout.d.ts" />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path="../Tops.App/registration.d.ts"/>
module Tops {
    export class SelectListObservable {
        private selectHandler : (selected: INameValuePair) => void;
        private subscription : any;
        options = ko.observableArray<INameValuePair>();
        selected = ko.observable<INameValuePair>();

        public constructor(selectHandler : (selected: INameValuePair) => void,
                           optionsList: INameValuePair[] = [],
                           defaultValue: any = null) {
            var me = this;
            me.options(optionsList);
            me.setValue(defaultValue);
            me.selectHandler = selectHandler;
            // me.subscription =  me.selected.subscribe(selectHandler);
        }

        public setValue(value: any) {
            var me = this;
            var options = me.options();
            var option = _.find(options,function(item: INameValuePair) {
                return item.Value == value;
            });
            me.selected(option);
        }

        public getValue() {
            var me = this;
            var selection = me.selected();
            return selection ? selection.Value : '';
        }

        public unsubscribe() {
            var me = this;
            me.subscription.dispose();
        }
        public subscribe() {
            var me = this;
            me.subscription = me.selected.subscribe(me.selectHandler);
        }
    }
}