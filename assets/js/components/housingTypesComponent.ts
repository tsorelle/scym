/**
 * Created by Terry on 12/21/2015.
 */
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path="../Tops.App/registration.d.ts"/>

module Tops {
    export class housingTypesComponent {
        housingTypes = ko.observableArray<IHousingType>();
        owner : IEventSubscriber;
        housingTypeForm = {
            id: ko.observable(),
            code: ko.observable<string>(),
            description: ko.observable<string>(),
            selectedCategory: ko.observable<INameValuePair>(),
            errorMessage: ko.observable<string>()
        };

        categories = ko.observableArray<INameValuePair>([
            {
                Name: 'None',
                Value: 0
            },
            {
                Name: 'Dorm',
                Value: 1
            },
            {
                Name: 'Cabin (lodge)',
                Value: 2
            },
            {
                Name: 'Motel Room',
                Value: 3
            },
        ]);

        private application : IPeanutClient;
        private peanut : Peanut;
        public constructor(application : IPeanutClient, owner : IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public getTypes() {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Getting housing types...');
            me.peanut.executeService('registration.GetHousingTypesEditList',request, me.handleGetTypesResponse)
                .always(function() {
                    me.application.hideWaiter();
                });

        }

        private handleGetTypesResponse = (serviceResponse:IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IHousingType[]>serviceResponse.Value;
                if (me.housingTypes().length > 0) {
                    me.notifyTypesUpdated(response);
                }
                var count = response.length;
                for (var i=0; i < count; i += 1) {
                    switch (response[i].category) {
                        case 0 :
                            (<any>response[i]).categoryName = 'None';
                            break;
                        case 1 :
                            (<any>response[i]).categoryName = 'Dorm';
                            break;
                        case 2 :
                            (<any>response[i]).categoryName = 'Cabin (lodge)';
                            break;
                        case 3 :
                            (<any>response[i]).categoryName = 'Motel Room';
                            break;
                        default :
                            (<any>response[i]).categoryName = '';
                            break;
                    }
                }
                
                me.housingTypes(response);
            }
        };

        private notifyTypesUpdated = (data: any) => {
            var me = this;
            if (me.owner) {
                me.owner.handleEvent('housingtypesupdated',data);
            }
        };
        
        
        //********* imported
        deleteHousingType  = (housingType: IHousingType) => {
            var me = this;
            me.housingTypeForm.id(housingType.housingTypeId);
            jQuery("#confirm-type-delete-modal").modal('show');
        };

        doDelete = () => {
            var me = this;
            jQuery("#confirm-type-delete-modal").modal('hide');

            var request = me.housingTypeForm.id();

            if (me.owner) {
                me.owner.handleEvent('housingtypesupdated');
            }

            // fake
            me.handleGetTypesResponse(me.getFakeResponse());
            me.application.hideWaiter();
            // todo: DeleteHousingType service
            /*
             me.peanut.executeService('registration.DeleteHousingType', request, me.handleGetHousingTypesResponse)
             .always(function () {
             me.application.hideWaiter();
             });
             */
        };

        showNewHousingTypeDialog = () => {
            var me = this;
            me.housingTypeForm.id(0);
            me.housingTypeForm.code('');
            me.housingTypeForm.description('');
            me.housingTypeForm.selectedCategory( null);
            me.housingTypeForm.errorMessage('');
            me.showForm();
        };


        updateHousingType = () => {
            var me = this;
            var request = me.validateDialog();
            if (request == null) {
                return;
            }

            // fake
            me.handleGetTypesResponse(me.getFakeResponse());
            me.application.hideWaiter();
            // todo: updateHousingType service
            /*
             me.peanut.executeService('registration.UpdateHousingHousingType', request, me.handleGetHousingTypesResponse)
             .always(function () {
             me.application.hideWaiter();
             });
             */
        };

        

        showForm() {
            var me = this;
            jQuery("#housingtype-update-modal").modal('show');
        }

        validateDialog = () => {

            var me = this;

            var request : IHousingType = {
                housingTypeId : me.housingTypeForm.id(),
                housingTypeCode: me.housingTypeForm.code().trim(),
                housingTypeDescription: me.housingTypeForm.description().trim(),
                category: 0
            };
            if (!request.housingTypeDescription) {
                me.housingTypeForm.errorMessage('A description is required.');
                return null;
            }
            if (!request.housingTypeCode) {
                me.housingTypeForm.errorMessage('A code is required. It must be a unique one word identifier for the type.');
                return null;
            }

            var selectedCategory = me.housingTypeForm.selectedCategory();
            if (selectedCategory) {
                request.category = selectedCategory.Value;
            }
            else {
                me.housingTypeForm.errorMessage('Please select a category.');
                return null;
            }

            var parts = request.housingTypeCode.split(' ');
            if (parts.length > 1) {
                me.housingTypeForm.errorMessage('A code cannot have spaces. It must be a unique one word identifier for the type.');
                return null;
            }

            request.housingTypeCode = request.housingTypeCode.toUpperCase();

            var duplicate = _.find(me.housingTypes(),function(item: IHousingType) {
                return item.housingTypeCode == request.housingTypeCode;
            },me);

            if (duplicate) {
                me.housingTypeForm.errorMessage('This type code is already in use. The code must be a unique one word identifier for the type.');
                return null;

            }

            jQuery("#housingtype-update-modal").modal('hide');
            return request;
        };


        //***************** FAKE ****************
        private getFakeResponse() {
            var fakeHousingTypes : IHousingType[] = [
                {
                    housingTypeId: 3,
                    housingTypeDescription: 'Night Owl Dorm for Women',
                    housingTypeCode: 'OWL',
                    category: 1
                },
                {
                    housingTypeId: 6,
                    housingTypeDescription: 'Family Cabin',
                    housingTypeCode: 'FAMILY',
                    category: 2
                },
                {
                    housingTypeId: 9,
                    housingTypeDescription: 'Camp Motel',
                    housingTypeCode: 'MOTEL',
                    category: 3
                }

            ];
            return new fakeServiceResponse(fakeHousingTypes);
        }

    }
}
