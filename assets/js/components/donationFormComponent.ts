/**
 * Created by Terry on 2/10/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />
///<reference path="selectListObservable.ts"/>


module Tops {
    export class donationFormComponent implements IDataEntryForm {

        private owner : IEventSubscriber;

        amount = ko.observable('');
        errorMessage = ko.observable('');
        notes = ko.observable('');
        donationTypes : selectListObservable;

        public constructor(owner: IEventSubscriber, donationTypes : INameValuePair[]) {
            var me = this;
            me.owner = owner;
            me.donationTypes = new selectListObservable(null,donationTypes);
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.clear();
            if (finalFunction) {
                finalFunction();
            }
        }

        visible = ko.observable(false);
        show = () => {
            var me = this;
            me.visible(true);
        };

        hide = () => {
            var me = this;
            me.visible(false);
        };


        setValues = (values: any) => {
            var me = this;
            // not implemented
        };

        getValues = ()=> {
            var me = this;
            var result : IDonationItem  = {
                amount: Number(me.amount()),
                donationTypeId : me.donationTypes.getValue(),
                notes: me.notes().trim()
            };
            return result;
        };

        clear = ()=> {
            var me = this;
            me.notes('');
            me.donationTypes.setValue(null);
            me.amount('');
        };

        getErrorMessage = () => {
            return this.errorMessage();
        };

        validate = (requireAmount = false) => {
            var me = this;
            me.errorMessage('');
            var amount = Peanut.validateCurrency(me.amount());
            if (amount === false) {
                me.errorMessage('Please enter a valid payment amount.');
                return false;
            }
            me.amount(amount);

            var typeId = me.donationTypes.getValue(0);
            if (typeId == 0) {
                me.errorMessage('Please select a donation type.');
                return false;
            }

            return true;
        };
        save = () => {
            var me = this;
            if ( me.validate() ) {
                var data = me.getValues();
                me.hide();
                me.owner.handleEvent('adddonation',data);
            }
        };

        cancel = () => {
            var me = this;
            me.clear();
            me.owner.handleEvent('donationformclosed');
        };

    }
}


