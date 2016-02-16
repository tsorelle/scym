///<reference path="paymentFormComponent.ts"/>
///<reference path="donationFormComponent.ts"/>
///<reference path="creditFormComponent.ts"/>
///<reference path="chargeFormComponent.ts"/>
///<reference path="USDollars.ts"/>
/**
 * Created by Terry on 1/4/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class registrationFinanceComponent implements IRegistrationComponent, IEventSubscriber {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner:IEventSubscriber;
        private lookups: IAccountLookupItem[] = [];

        registrationId = ko.observable();
        updating = ko.observable(false);
        donations = ko.observableArray<IDonationDisplayItem>();
        payments = ko.observableArray<IPaymentDisplayItem>();
        charges = ko.observableArray<IChargeDisplayItem>();
        credits = ko.observableArray<ICreditDisplayItem>();

        paymentForm:IDataEntryForm;
        donationForm:IDataEntryForm;
        chargeForm:IDataEntryForm;
        creditForm:IDataEntryForm;
        selectedAccountItem : IAccountDisplayItem = null;
        deleteMessage = ko.observable('');

        registrationName = ko.observable('');
        registrationCode = ko.observable('');
        paymentFormHidden = ko.observable(true);
        donationFormHidden = ko.observable(true);
        chargeFormHidden = ko.observable(true);
        creditFormHidden = ko.observable(true);
        notesView = ko.observable('');

        balance = ko.observable(0.00);
        totalDue = ko.observable('');
        accountStatus = ko.observable('');
        paymentTotal = ko.observable('');
        creditTotal = ko.observable('');
        chargeTotal = ko.observable('');
        donationTotal = ko.observable('');

        currentDate = ko.pureComputed<string>(
            function() {
                return (new Date()).toDateString()
            }
        );

        public constructor(application:IPeanutClient, owner:IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        private clearDisplay() {
            var me = this;
            me.payments([]);
            me.charges([]);
            me.credits([]);
            me.donations([]);
            me.balance(0.00);
            me.totalDue('');
            me.accountStatus('');
            me.paymentTotal('');
            me.creditTotal('');
            me.chargeTotal('');
            me.donationTotal('');
            me.paymentFormHidden(true);
            me.creditFormHidden(true);
            me.chargeFormHidden(true);
            me.donationFormHidden(true);
        }

        private prepareService(registrationId:any) {
            var me = this;
            me.clearDisplay();
            me.registrationId(0);
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting account details...');
            return {
                registrationId: registrationId,
                includeLookups: false
            };
        }

        showPaymentForm = () => {
            var me = this;
            if (me.paymentForm) {
                me.showForm(me.paymentForm,me.paymentFormHidden);
            }
            else {
                me.paymentForm = new paymentFormComponent(me);
                me.application.registerAndBindComponent( 'newpayment-form', me.paymentForm,function() {
                    me.showForm(me.paymentForm,me.paymentFormHidden);
                });
            }
        };
        showDonationForm = () => {
            var me = this;
            if (me.donationForm) {
                me.showForm(me.donationForm,me.donationFormHidden);
            }
            else {
                me.application.bindComponent('donation-form',
                    function () {
                        var lookup = me.getLookup('donation');
                        me.donationForm = new donationFormComponent(me,lookup);
                        return me.donationForm;
                    }, function() {
                        me.showForm(me.donationForm,me.donationFormHidden);
                    });
            }
        };

        showCreditForm = () => {
            var me = this;
            if (me.creditForm) {
                me.showForm(me.creditForm,me.creditFormHidden);
            }
            else {
                me.application.bindComponent('credit-form',
                    function () {
                        var lookup = me.getLookup('credit');
                        me.creditForm = new creditFormComponent(me,lookup);
                        return me.creditForm;
                    }, function() {
                        me.showForm(me.creditForm,me.creditFormHidden);
                    });
            }
        };

        showChargeForm = () => {
            var me = this;
            var me = this;
            if (me.chargeForm) {
                me.showForm(me.chargeForm,me.chargeFormHidden);
            }
            else {
                me.application.bindComponent('charge-form',
                    function () {
                        var lookup = me.getLookup('fee');
                        me.chargeForm = new chargeFormComponent(me,lookup);
                        return me.chargeForm;
                    }, function() {
                        me.showForm(me.chargeForm,me.chargeFormHidden);
                    });
            }

        };

        show = () => {
            jQuery('#finance-component-wrapper');
        };

        public initialize(registrationId:any, finalFunction?:() => void) {
            var me = this;
            me.application.loadResources(['selectListObservable.js'],function() {
                var request = me.prepareService(registrationId);
                request.includeLookups = true;
                me.peanut.executeService('registration.GetAccountDetails', request, function (serviceResponse:IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IAccountDetails>serviceResponse.Value;
                        me.registrationCode(response.registrationCode);
                        me.registrationName(response.registrationName);
                        me.assignAccountItems(response);
                        me.lookups = response.lookups;
                        jQuery('#finance-view-container').show();
                        if (finalFunction) {
                            finalFunction();
                        }
                    }
                })
                .always(function () {
                    me.application.hideWaiter();
                });
            });
        }

        private getLookup(lookupType:string) {
            var me = this;
            return _.filter(me.lookups, function (item:IAccountLookupItem) {
                return item.lookupType == lookupType;
            });
        }

        refreshAccount() {
            var me = this;
            me.getAccount(me.registrationId());
        }

        getAccount(registrationId:any) {
            var me = this;
            var request = me.prepareService(registrationId);
            me.peanut.executeService('registration.GetAccountDetails', request, function (serviceResponse:IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IAccountDetails>serviceResponse.Value;
                        me.assignAccountItems(response);
                        me.show();
                    }
                })
                .always(function () {
                    me.application.hideWaiter();
                });
        }

        assignAccountItems = (response:IAccountDetails) => {
            var me = this;
            me.registrationId(response.registrationId);
            me.payments(response.payments);
            me.charges(response.charges);
            me.credits(response.credits);
            me.donations(response.donations);
            me.calculateTotals(response);
            me.owner.handleEvent('accountloaded', response.registrationId);
        };

        hideForm = (form :IDataEntryForm, hideFlag : KnockoutObservable<boolean>) => {
            var me = this;
            hideFlag(true);
            form.hide();
        };

        showForm = (form :IDataEntryForm, hideFlag : KnockoutObservable<boolean>) => {
            var me = this;
            hideFlag(false);
            form.clear();
            form.show();
        };
        handleEvent = (eventName:string, data?:any)=>{
            var me = this;
            switch(eventName) {
                case 'addpayment'    : me.hideForm(me.paymentForm, me.paymentFormHidden); me.updateAccount(data, 'payment' ,'add'); break;
                case 'addcharge'     : me.hideForm(me.chargeForm, me.chargeFormHidden); me.updateAccount(data, 'charge'  ,'add'); break;
                case 'addcredit'     : me.hideForm(me.creditForm, me.creditFormHidden); me.updateAccount(data, 'credit'  ,'add'); break;
                case 'adddonation'   : me.hideForm(me.donationForm, me.donationFormHidden); me.updateAccount(data, 'donation','add'); break;
                case 'removepayment' : me.updateAccount(data, 'payment' ,'remove'); break;
                case 'removecharge'  : me.updateAccount(data, 'charge'  ,'remove'); break;
                case 'removecredit'  : me.updateAccount(data, 'credit'  ,'remove'); break;
                case 'removedonation': me.updateAccount(data, 'donation','remove'); break;
                case 'paymentformclosed' : me.hideForm(me.paymentForm,me.paymentFormHidden);   break;
                case 'chargeformclosed'  : me.hideForm(me.chargeForm , me.chargeFormHidden);   break;
                case 'creditformclosed'  : me.hideForm(me.creditForm , me.creditFormHidden);   break;
                case 'donationformclosed': me.hideForm(me.donationForm , me.donationFormHidden); break;
            }
        };

        updateAccount(data: any, itemType: string, action: string) {
            var me = this;
            var request = {
                registrationId : me.registrationId(),
                itemType : itemType,
                data: data,
                action : action
            };
            me.application.hideServiceMessages();
            var message = action == 'add' ? 'Adding' : 'Removing';
            me.application.showWaiter(
                message + ' a '+itemType+'...'
            );
            me.peanut.executeService('registration.updateAccount',request,
                function(serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IAccountDetails>serviceResponse.Value;
                        me.assignAccountItems(response);
                        var balanceChangeNotice = {
                            registrationId: me.registrationId(),
                            balance: me.balance()
                        };
                        me.owner.handleEvent('balancechanged',balanceChangeNotice);}
                }
            ).always(function() {
                me.application.hideWaiter();
            });
        }

        private sumItems(items: IAccountItem[]) {
            var total = 0.0;
            _.each(items,function (item: IAccountItem){
                total += Number(item.amount);
            });
            return USDollars.roundNumber(total);
        }

        private formatCost(value: number, observable: KnockoutObservable<string>) {
            var text =  (value > 0.00) ? '(' + USDollars.toUSD(value) +  ' total)' : '(none)';
            observable(text);
        }

        private setBalance(balance : any) {
            var me = this;
            balance = USDollars.roundNumber(balance);
            me.balance(balance);
            var balanceMessage = USDollars.balanceMessage(balance);
            me.accountStatus(balanceMessage);
        }

        private calculateTotals(response: IAccountDetails) {
            var me = this;
            var paymentTotal = me.sumItems(response.payments);
            var creditTotal = me.sumItems(response.credits);
            var chargeTotal = me.sumItems(response.charges);
            var donationTotal= me.sumItems(response.donations);
            var totalDue =  USDollars.roundNumber((chargeTotal + donationTotal) - creditTotal);
            var balance = (chargeTotal + donationTotal) - (creditTotal + paymentTotal);

            me.setBalance(balance);
            me.formatCost(totalDue,me.totalDue);
            me.formatCost(paymentTotal,me.paymentTotal);
            me.formatCost(creditTotal,me.creditTotal);
            me.formatCost(chargeTotal,me.chargeTotal);
            me.formatCost(donationTotal,me.donationTotal);
        }

        showNotes = (item: IAccountItem) => {
            var me = this;
            me.notesView(item.notes);
            jQuery("#finance-item-notes-modal").modal('show');
        };

        deleteItem = (item: IAccountDisplayItem) => {
            var me = this;
            me.selectedAccountItem = item;
            me.deleteMessage('Delete this ' + item.itemType + '?')
            jQuery("#confirm-acount-item-delete-modal").modal('show');
        };

        removeItem = () => {
            var me = this;
            jQuery("#confirm-acount-item-delete-modal").modal('hide');
            me.updateAccount(me.selectedAccountItem.id,me.selectedAccountItem.itemType,'remove');
        }
    }
}

