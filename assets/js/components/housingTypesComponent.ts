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
        housingTypes = ko.observableArray<IHousingTypeDisplayItem>();
        owner : IEventSubscriber;
        changed = ko.observable(false);
        updateRequest = {
            updated : [],
            added : []
        };

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
                var response = <IHousingTypeDisplayItem[]>serviceResponse.Value;
                if (me.housingTypes().length > 0) {
                    me.notifyTypesUpdated(response);
                }
                me.changed(false);
                me.updateRequest.added = [];
                me.updateRequest.updated = [];
                me.housingTypes(response);
            }
        };

        private notifyTypesUpdated = (data: any) => {
            var me = this;
            if (me.owner) {
                me.owner.handleEvent('housingtypesupdated',data);
            }
        };
        
        
        showNewHousingTypeDialog = () => {
            var me = this;
            me.housingTypeForm.id(0);
            me.housingTypeForm.code('');
            me.housingTypeForm.description('');
            me.housingTypeForm.selectedCategory( null);
            me.housingTypeForm.errorMessage('');
            jQuery("#housingtype-update-modal").modal('show');
        };

        addHousingType = () => {
            var me = this;
            var newType = me.validateDialog();
            if (newType == null) {
                return;
            }

            me.updateRequest.added.push(
                {
                    housingTypeCode: newType.housingTypeCode,
                    housingTypeDescription: newType.housingTypeDescription,
                    category: newType.category,
                }
            );

            (<any>newType).active = true;

            var list = me.housingTypes();
            list.push(newType);
            me.housingTypes(_.sortBy(list, 'housingTypeDescription'));

            me.changed(true);
        };

        toggleActive = (selelctedType : IHousingTypeDisplayItem) => {
            var me = this;
            if (selelctedType != null) {
               me.changed(true);
                var existing = _.find(me.updateRequest.updated, function(item: any) {
                    return item.id == selelctedType.housingTypeId;
                });
                if (existing == null) {
                    me.updateRequest.updated.push(
                        {
                            id: selelctedType.housingTypeId,
                            active: (selelctedType.active == 0)
                        }
                    );
                }
                else {
                    existing.active =  (selelctedType.active == 0);
                }
            }
            return true; // requied to keep click event from re-checking the checkbox
        };

        saveChanges() {
            var me = this;
            if (me.changed()) {
                me.application.showWaiter('Updating...');
                 me.peanut.executeService('registration.UpdateHousingTypes', me.updateRequest, me.handleGetTypesResponse)
                    .always(function () {
                    me.application.hideWaiter();
                 });
            }
        }

        validateDialog = () => {

            var me = this;
            var selectedCategory = me.housingTypeForm.selectedCategory();


            var result : IHousingTypeDisplayItem = {
                housingTypeId : me.housingTypeForm.id(),
                housingTypeCode: me.housingTypeForm.code(),
                housingTypeDescription: me.housingTypeForm.description().trim(),
                active: 1,
                category: 0,
                categoryName: ''
            };

            if (!result.housingTypeDescription) {
                me.housingTypeForm.errorMessage('A description is required.');
                return null;
            }
            if (!result.housingTypeCode) {
                me.housingTypeForm.errorMessage('A code is required. It must be a unique one word identifier for the type.');
                return null;
            }

            var selectedCategory = me.housingTypeForm.selectedCategory();
            if (selectedCategory) {
                result.category = selectedCategory.Value;
                result.categoryName = selectedCategory.Name;
            }
            else {
                me.housingTypeForm.errorMessage('Please select a category.');
                return null;
            }

            var parts = result.housingTypeCode.split(' ');
            if (parts.length > 1) {
                me.housingTypeForm.errorMessage('A code cannot have spaces. It must be a unique one word identifier for the type.');
                return null;
            }

            result.housingTypeCode = result.housingTypeCode.toUpperCase();

            var duplicate = _.find(me.housingTypes(),function(item: IHousingType) {
                return item.housingTypeCode == result.housingTypeCode;
            },me);

            if (duplicate) {
                me.housingTypeForm.errorMessage('This type code is already in use. The code must be a unique one word identifier for the type.');
                return null;

            }

            jQuery("#housingtype-update-modal").modal('hide');
            return result;
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
