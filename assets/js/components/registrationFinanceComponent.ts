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


        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
        }

        getAccount(registrationId: any) {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');


            // fake
            var response = me.getFakeRegistrationResponse(registrationId);
            me.handleGetAccountResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleGetAccountResponse)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        }

        private handleGetAccountResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = serviceResponse.Value;
                me.registrationId(response.registrationId);
                me.owner.handleEvent('accountloaded',response.registrationId);
            }
        };


        /*** fakes ***********/
        private getFakeRegistrationResponse(registrationId: any) {
            var response = {
                registrationId: registrationId
            };

            return new fakeServiceResponse(response);
        }




    }
}

 // Tops.TkoComponentLoader.addVM('component-name',Tops.registrationFinanceComponent);
