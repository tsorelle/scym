/**
 * Created by Terry on 10/30/2015.
 * See documentation in modalConfirmComponent-doc.txt
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='TkoComponentLoader.ts' />
module Tops {
    export class modalConfirmComponent {
        public confirmClick : ()=> void;
        public headerText : KnockoutObservable<string>;
        public bodyText : KnockoutObservable<string>;
        public modalId : KnockoutObservable<string>;
        public okLabel : KnockoutObservable<string>;
        public cancelLabel : KnockoutObservable<string>;
        constructor(params : any) {
            var me = this;
            me.modalId = ko.observable(params.id);
            me.confirmClick = params.confirmClick;
            me.headerText = (typeof params.headerText == 'string') ?  ko.observable(params.headerText) : params.headerText;
            me.bodyText =  (typeof params.bodyText == 'string') ?  ko.observable(params.bodyText) : params.bodyText;
            var buttonSet = (params.buttonSet) ? params.buttonSet : 'okcancel';
            switch(buttonSet) {
                case 'yesno' :
                    me.okLabel = ko.observable('Yes');
                    me.cancelLabel = ko.observable('No');
                    break;
                default:
                    me.okLabel = ko.observable('Ok');
                    me.cancelLabel = ko.observable('Cancel');
                    break;
            }
        }
    }
}
Tops.TkoComponentLoader.addVM('modal-confirm',Tops.modalConfirmComponent);