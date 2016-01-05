/**
 * Created by Terry on 1/4/2016.
 */
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path="searchListObservable.ts"/>
module Tops {
    export class registrationLookupComponent  {
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
            me.registrationsList = new searchListObservable(3,4);
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
            me.findRegistrations(me.searchType());

        };

        public findRegistrationsByName = () => {
            var me = this;
            me.searchType('');
            this.onSelected(0);
            var value = me.registrationsList.searchValue().trim();
            if (value) {
                me.findRegistrations('name',value);
            }
        };

        private findRegistrations = (searchType : string = 'name', searchValue : string = null) => {
            var me = this;
            me.registrationsList.reset();
            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');

            var request =  {
                searchType: searchType,
                searchValue: searchValue
            };

            me.peanut.executeService('registration.FindRegistrations',request, me.handleFindRegistrationsResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
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


