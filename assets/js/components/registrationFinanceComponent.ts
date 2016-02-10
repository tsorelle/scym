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
    export class registrationFinanceComponent implements IRegistrationComponent{
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        registrationId = ko.observable();
        updating = ko.observable(false);
        donations = ko.observableArray<IDonationDisplayItem>();
        payments  = ko.observableArray<IPaymentDisplayItem>();
        charges   = ko.observableArray<IChargeDisplayItem>();
        credits   = ko.observableArray<ICreditDisplayItem>();

        paymentForm : IDataEntryForm;
        donationForm : IDataEntryForm;
        chargeForm : IDataEntryForm;
        creditForm : IDataEntryForm;

        balance: any;

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        private prepareService(registrationId : any) {
            var me = this;
            me.registrationId(0);
            me.payments([]);
            me.charges([]);
            me.credits([]);
            me.donations([]);
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting account details...');
            return {
                registrationId : registrationId,
                includeLookups : false
            };
        }

        public initialize(registrationId: any, finalFunction? : () => void) {
            var me = this;
            var request = me.prepareService(registrationId);
            request.includeLookups = true;
            me.peanut.executeService('registration.GetAccountDetails',request,function(serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IAccountDetails>serviceResponse.Value;
                        me.assignAccountItems(response);
                        me.application.loadResources([], function() {
                           // todo: create instances and bind components
                            if (finalFunction) {
                                finalFunction();
                            }
                        });
                    }
                })
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private getLookup(lookups: IAccountLookupItem[], lookupType : string) {
            return _.filter(lookups, function(item: IAccountLookupItem) {
                return item.lookupType == lookupType;
            });
        }

        getAccount(registrationId: any) {
            var me = this;
            var request = me.prepareService(registrationId);
            me.peanut.executeService('registration.GetAccountDetails',request,function(serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IAccountDetails>serviceResponse.Value;
                        me.assignAccountItems(response);
                    }
                })
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        assignAccountItems = (response: IAccountDetails) => {
            var me = this;
            me.registrationId(response.registrationId);
            me.payments(response.payments);
            me.charges(response.charges);
            me.credits(response.credits);
            me.donations(response.donations);
            me.owner.handleEvent('accountloaded', response.registrationId);
        };
    }
}

 // Tops.TkoComponentLoader.addVM('component-name',Tops.registrationFinanceComponent);
