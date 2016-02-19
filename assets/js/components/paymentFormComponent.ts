/**
 * Created by Terry on 2/13/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class paymentFormComponent implements IDataEntryForm, IEventSubscriber {

        protected owner : IEventSubscriber;

        amount = ko.observable('');
        notes = ko.observable('');
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
            this.amount(values.amount);
        };

        setAmount = (amount:any)=> {
            this.amount(amount);
        };

        getValues = ()=> {
            var me = this;
            var type = me.paymentType();
            var result : IPaymentItem =  {
                amount: Number(me.amount()),
                type: type,
                payor: me.payor(),
                notes: me.notes(),
                checkNumber: type == 'cash' ? 'cash' : me.checkNumber()
            };
            return result;
        };

        clear = ()=> {
            var me = this;
            me.amount('');
            me.paymentType('check');
            me.checkNumber('');
            me.notes('');
            me.payor('');
        };

        getErrorMessage = () => {
            return this.errorMessage();
        };

        validate = (requireAmount = false) => {
            var me = this;
            me.errorMessage('');
            var hasAmount = true;
            var value = me.amount();
            if (typeof value == 'string') {
                value = value.trim();
                hasAmount = value.trim() ? true : false;
            }

            if (requireAmount || hasAmount) {
                var amount = Peanut.validateCurrency(value);
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
        save = () => {
            var me = this;
            if ( me.validate() ) {
                var payment = me.getValues();
                me.owner.handleEvent('addpayment',payment);
                me.hide();
            }
        };

        cancel = () => {
            var me = this;
            me.clear();
            me.owner.handleEvent('paymentformclosed');
        };

        fillBalance = () => {
            var me = this;
            me.owner.handleEvent('registrationdatarequest','balance');
        };

        fillPayor = () => {
            var me = this;
            me.owner.handleEvent('registrationdatarequest','name');
        };


        handleEvent = (eventName:string, data?:any)=> {
            var me = this;
            switch(eventName) {
                case 'registrationdataresponse' :
                    if (data && data.name && data.value) {
                        switch (data.name) {
                            case 'name' :
                                me.payor(data.value);
                                break;
                            case 'balance' :
                                me.amount(data.value);
                                break;
                        }
                        break;
                    }
            }
        };
    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.paymentFormComponent);

