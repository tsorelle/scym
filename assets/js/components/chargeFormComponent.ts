///<reference path="selectListObservable.ts"/>
/**
 * Created by Terry on 2/10/2016.
 */
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

module Tops {
    export class chargeFormComponent implements IDataEntryForm {
        private owner : IEventSubscriber;

        amount = ko.observable('');
        errorMessage = ko.observable('');
        feeTypesList : selectListObservable;
        basis = ko.observable('');
        notes = ko.observable('');

        public constructor(owner: IEventSubscriber = null, feeTypes: INameValuePair[]) {
            var me = this;
            me.owner = owner;
            me.feeTypesList = new selectListObservable(null,feeTypes);
            me.feeTypesList.selected.subscribe( function(item : INameValuePair) {
                if ((!item) || item.Name == 'Other') {
                    me.basis('');
                }
                else  {
                    me.basis(item.Name);
                }
            });
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
            var result : IChargeItem = {
                amount: Number(me.amount()),
                basis: me.basis().trim(),
                notes: me.notes().trim(),
                feeTypeId: me.feeTypesList.getValue()
            };

            return result;
        };

        clear = ()=> {
            var me = this;
            me.amount('');
            me.basis('');
            me.notes('');
            me.errorMessage('');
            me.feeTypesList.setValue(null);
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

            var typeId = me.feeTypesList.getValue(0);
            if (typeId == 0) {
                me.errorMessage('Please select a charge type.');
                return false;
            }

            if (!me.basis().trim()) {
                me.errorMessage('Please enter a description.');
                return false;
            }

            return true;
        };

        save = () => {
            var me = this;
            if ( me.validate() ) {
                var data = me.getValues();
                me.hide();
                me.owner.handleEvent('addcharge',data);
            }
        };

        cancel = () => {
            var me = this;
            me.clear();
            me.owner.handleEvent('chargeformclosed');
        };

    }
}



