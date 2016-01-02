/**
 * Created by Terry on 12/22/2015.
 */
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path="searchListObservable.ts"/>
module Tops {
    export class housingLookupComponent  {
        private application : IPeanutClient;
        private peanut : Peanut;
        
        registrationsList : searchListObservable;
        showMessagesButton = ko.observable(false);
        public searchFormVisible = ko.observable(true);
        searchType = ko.observable('');
        private owner : IEventSubscriber;

        public constructor(application : IPeanutClient, owner: IEventSubscriber) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.registrationsList = new searchListObservable(4,3);
        }

        public getNext = () => {
            this.onSelected(0);
            this.onSelected(1);
        };

        public onSelected = (regId: number) => {
            var me = this;
            if (me.owner) {
                me.owner.handleEvent('registrationselected', regId);
            }
        };

        public sendConfirmations = () => {
            var me = this;
            jQuery("#confirm-send-all-confirmations").modal('show');
        };

        public doSendConfirmations = () => {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Queing messages...');


            // fake
            var response = null;
            me.handleSendConfirmationsResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleSendConfirmationsResponse)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        };

        private handleSendConfirmationsResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            me.showMessagesButton(false);
            jQuery("#confirm-send-all-confirmations").modal('hide');
        };



        public getRegistration = (item : INameValuePair) => {
            var me = this;
            // me.registrationsList.reset();
            me.onSelected(parseInt(item.Value));
        };

        public findAllRegistrations = () => {
            var me = this;
            me.searchType('incomplete');
            jQuery("#search-all-modal").modal('show');
        };

        public searchAll = () => {
            var me = this;
            jQuery("#search-all-modal").modal('hide');
            this.onSelected(0);
            me.findRegistrations();

        };

        public findRegistrationsByName = () => {
            var me = this;
            me.searchType('');
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

            var request;

            if (searchValue) {
                request = {
                    type: 'name',
                    value: searchValue
                };
            }
            else {
                request = {
                    type: 'all',
                    value: me.searchType()
                };
            }

            // Fake -----------
            me.handleFindRegistrationsResponse(me.getFakeSearchResult());
            me.application.hideWaiter();

            /*
            me.peanut.executeService('registration.FindRegistrations',request, me.handleFindRegistrationsResponse)
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
                me.showMessagesButton(
                    me.searchType() == 'unconfirmed' &&
                    me.registrationsList.foundCount() > 0);
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

