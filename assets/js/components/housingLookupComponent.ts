/**
 * Created by Terry on 12/22/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path="searchListObservable.ts"/>    
module Tops {
    export class housingLookupComponent  {
        private application : IPeanutClient;
        private peanut : Peanut;
        
        registrationsList : searchListObservable;
        public searchFormVisible = ko.observable(true);

        public constructor(application : IPeanutClient, handler : (regId: number) => void) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.onSelected = handler;
            me.registrationsList = new searchListObservable(4,3);
        }

        public getNext = () => {
            this.onSelected(0);
            this.onSelected(1);
        };

        public onSelected : (regId: number) => void;

        public getRegistration = (item : INameValuePair) => {
            var me = this;
            // me.registrationsList.reset();
            me.onSelected(parseInt(item.Value));
        };

        public findAllRegistrations = () => {
            var me = this;
            this.onSelected(0);
            me.findRegistrations();
        };

        public findRegistrationsByName = () => {
            var me = this;
            this.onSelected(0);
            var value = me.registrationsList.searchValue().trim();
            if (value) {
                me.findRegistrations(value);
            }
        };

        private findRegistrations = (searchValue : string = null) => {
            var me = this;
            me.registrationsList.reset();
            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');

            // Fake -----------
            me.handleFindRegistrationsResponse(me.getFakeSearchResult());
            me.application.hideWaiter();

            /*
            me.peanut.executeService('registration.FindRegistrations',searchValue, me.handleFindRegistrationsResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
            */
        };


        private handleFindRegistrationsResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.registrationsList.setList(list);
                me.registrationsList.searchValue('');
            }
        };

        /*** fakes ***/

        private getFakeSearchResult() {
            var response : INameValuePair[] = [
                {
                    Name: 'Terry SoRelle and Liz Yeats',
                    Value: '1'
                },
                {
                    Name: 'Joe Snow and Barabra Blow',
                    Value: '2'
                },
                {
                    Name: 'Winston Salem and family',
                    Value: '3'
                },
                {
                    Name: 'Joan Crawford',
                    Value: '4'
                },
                {
                    Name: 'The bunnies',
                    Value: '5'
                }
            ];
            return new fakeServiceResponse(response);
        }

    }
}

