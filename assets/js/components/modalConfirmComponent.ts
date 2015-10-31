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
        constructor(params : any) {
            var me = this;
            me.modalId = ko.observable(params.id);
            me.confirmClick = params.confirmClick;
            me.headerText = (typeof params.headerText == 'string') ?  ko.observable(params.headerText) : params.headerText;
            me.bodyText =  (typeof params.bodyText == 'string') ?  ko.observable(params.bodyText) : params.bodyText;
        }
    }
}
Tops.TkoComponentLoader.addVM('modal-confirm',Tops.modalConfirmComponent);