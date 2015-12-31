/**
 * Created by Terry on 12/21/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.App/registration.d.ts" />

module Tops {
    export class housingUnitsComponent implements IEventSubscriber {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner: IEventSubscriber;

        housingUnits = ko.observableArray<IHousingUnit>();
        housingTypes = ko.observableArray<ILookupItem>();
        unitForm = {
            heading : ko.observable(),
            unitId : ko.observable(0),
            unitname: ko.observable<string>(),
            description: ko.observable<string>(),
            capacity: ko.observable<string>(),
            selectedHousingType: ko.observable<ILookupItem>(),
            errorMessage: ko.observable<string>(),
        };

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public getUnits() {
            var me = this;
            var request = null;
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting housing units...');

            // fake
            me.handleGetUnitsResponse(me.getFakeResponse());
            me.application.hideWaiter();

            /*
            me.peanut.executeService('registration.GetHousingUnits', request, me.handleGetUnitsResponse)
                .always(function () {
                    me.application.hideWaiter();
                });
               */
        }

        private handleGetUnitsResponse = (serviceResponse:IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetHousingUnitsResponse>serviceResponse.Value;
                if (me.housingUnits().length > 0) {
                    me.notifyUnitsUpdate(response.units);
                }
                me.housingTypes(response.housingTypes);
                me.housingUnits(response.units);
            }
        };

        deleteUnit  = (unit: IHousingUnit) => {
            var me = this;
            me.unitForm.unitId(unit.housingUnitId);
            jQuery("#confirm-unit-delete-modal").modal('show');
        };

        doDeleteUnit = () => {
            var me = this;
            jQuery("#confirm-unit-delete-modal").modal('hide');
            var request = me.unitForm.unitId;

            // fake
            me.handleGetUnitsResponse(me.getFakeResponse());
            me.application.hideWaiter();


            /*
             me.peanut.executeService('registration.DeleteHousingUnit', request, me.handleGetUnitsResponse)
             .always(function () {
             me.application.hideWaiter();
             });
             */
        };

        showEditUnitDialog = (unit: IHousingUnit) => {
            var me = this;
            me.unitForm.heading('Update Housing Unit');
            me.unitForm.unitId(unit.housingUnitId);
            me.unitForm.unitname(unit.unitname);
            me.unitForm.description(unit.description);
            me.unitForm.capacity(unit.capacity.toString());
            me.unitForm.selectedHousingType(me.getHousingType(unit.housingTypeId));
            me.unitForm.errorMessage('');
            me.showForm();
        };

        showNewUnitDialog = () => {
            var me = this;
            me.unitForm.heading('New Housing Unit');
            me.unitForm.unitId(0);
            me.unitForm.unitname('');
            me.unitForm.description('');
            me.unitForm.capacity('2');
            me.unitForm.selectedHousingType(null);
            me.unitForm.errorMessage('');
            me.showForm();
        };


        updateUnit = () => {
            var me = this;
            var request = me.validateDialog();
            if (request == null) {
                return;
            }

            // fake
            me.handleGetUnitsResponse(me.getFakeResponse());
            me.application.hideWaiter();

            /*
             me.peanut.executeService('registration.UpdateHousingUnit', request, me.handleGetUnitsResponse)
             .always(function () {
             me.application.hideWaiter();
             });
             */
        };

        getHousingType = (id:number) => {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find(me.housingTypes(), function (item:ILookupItem) {
                    return item.Key == id;
                }, me);
            }
            return selected;
        };

        notifyUnitsUpdate = (data: any) => {
            var me = this;
            if (me.owner) {
                me.owner.handleEvent('housingunitsupdated',data);
            }
        };

        handleEvent = (eventName:string, data:any = null)=> {
            var me = this;
            if (eventName == 'housingtypesupdated') {
                me.housingTypes(data);
            }
        };


        hideForm() {
            jQuery("#unit-update-modal").modal('hide');
        }

        showForm() {
            var me = this;
            jQuery("#unit-update-modal").modal('show');
        }

        hideConfirmForm() {
            jQuery("#confirm-unit-delete-modal").modal('hide');
        }

        showConfirmForm() {
            var me = this;
            jQuery("#confirm-unit-delete-modal").modal('show');
        }

        private validateDialog() {

            var me = this;
            var housingTypeId = -1;
            var selectedType = me.unitForm.selectedHousingType();
            if (selectedType) {
                housingTypeId = selectedType.Key;
            }
            var request : IHousingUnitUpdateRequest = {
                unitId : me.unitForm.unitId(),
                unitname: me.unitForm.unitname().trim(),
                description: me.unitForm.description(),
                capacity: parseInt(me.unitForm.capacity()),
                housingTypeId: housingTypeId
            };

            if (!request.unitname) {
                me.unitForm.errorMessage('A unit name is required.');
                return null;
            }
            if (isNaN(request.capacity)) {
                me.unitForm.errorMessage('Capacity should be a valid whole number.');
                return null;
            }
            if (request.housingTypeId < 1) {
                me.unitForm.errorMessage('Please select a housing type');
                return null;
            }
            jQuery("#unit-update-modal").modal('hide');
            return request;
        };



        //***************** FAKE ****************
        private getFakeResponse() {
            var fakeHousingTypes:ILookupItem[] = [
                {
                    Key: 3,
                    Text: 'Night Owl Dorm for Women',
                    Description: '',
                },
                {
                    Key: 6,
                    Text: 'Family Cabin',
                    Description: '',
                },
                {
                    Key: 9,
                    Text: 'Camp Motel',
                    Description: '',
                }

            ];

            var fakeUnits:IHousingUnit[] = [
                {
                    housingUnitId: 1,
                    unitname: 'Cabin A1',
                    description: '',
                    capacity: 14,
                    occupants: 2,
                    housingTypeId: 3, // 'OWLW',
                    housingTypeName: 'Night Owl Women',
                },
                {
                    housingUnitId: 27,
                    unitname: 'Cabin J1',
                    description: '',
                    capacity: 6,
                    occupants: 0,
                    housingTypeId: 6, // 'FAMILY',
                    housingTypeName: 'Family Cabin',
                },
                {
                    housingUnitId: 87,
                    unitname: 'Motel 6',
                    description: '',
                    capacity: 2,
                    occupants: 0,
                    housingTypeId: 9,
                    housingTypeName: 'Camp Motel'
                }
            ];

            var responseData:IGetHousingUnitsResponse = {
                units: fakeUnits,
                housingTypes: fakeHousingTypes
            };
            return new fakeServiceResponse(responseData);
        }


    }

}
