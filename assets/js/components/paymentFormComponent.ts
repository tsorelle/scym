/**
 * Created by Terry on 1/5/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class paymentFormComponent implements IDataEntryForm {

        private owner : IEventSubscriber;

        amount = ko.observable('');
        paymentType = ko.observable('check');
        paymentTypes = ['check','cash'];
        checkNumber = ko.observable('');
        errorMessage = ko.observable('');
        payor = ko.observable('');


        public constructor(owner: IEventSubscriber = null) {
            var me = this;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
        }

        setValues = (values: any) => {
            var me = this;
            this.amount(values.amount);
        };

        setAmount = (amount:any)=> {
            this.amount(amount);
        };

        getValues = ()=> {
            var me = this;
            var type = me.paymentType();
            return {
                amount: me.amount(),
                type: type,
                payor: me.payor(),
                checkNumber: type == 'cash' ? 'cash' : me.checkNumber()
            };
        };

        clear = ()=> {
            var me = this;
            me.amount('');
            me.paymentType('check');
            me.checkNumber('');
            me.payor('');
        };

        getErrorMessage = () => {
            return this.errorMessage();
        };

        validate = (requireAmount = false) => {
            var me = this;
            me.errorMessage('');
            if (requireAmount || me.amount().trim()) {
                var amount = Peanut.validateCurrency(me.amount());
                if (amount === false) {
                    me.errorMessage('Please enter a valid payment amount.');
                    return false;
                }

                if (me.paymentType() == 'check') {
                    var checkNumber = me.checkNumber().trim();
                    if (!checkNumber) {
                        me.errorMessage('A check number is required.');
                        return false;
                    }
                    else {
                        me.checkNumber(checkNumber)
                    }
                }

                if (!me.payor().trim()) {
                    me.errorMessage('Please enter a payor');
                    return false;
                }
            }



            return true;
        };

    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.paymentFormComponent);

