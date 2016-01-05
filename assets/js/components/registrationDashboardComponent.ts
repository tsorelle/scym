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
    export class registrationDashboardComponent implements IRegistrationComponent {

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

        public getRegistration(registrationId: any) {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');


            // fake
            var response = me.getFakeRegistrationResponse(registrationId);
            me.handleGetRegistrationResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleGetRegistrationResponse)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        }

        closeDashboard = () => {
            var me = this;
            me.owner.handleEvent('dashboardclosed');
        };

        private handleGetRegistrationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = serviceResponse.Value;
                me.registrationId(response.registrationId);
                me.owner.handleEvent('registrationdashboardloaded',response.registrationId);
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

// Tops.TkoComponentLoader.addVM('component-name',Tops.registrationDashboardComponent);
