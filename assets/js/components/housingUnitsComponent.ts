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
        private owner:IEventSubscriber;
        private refreshNeeded = false;

        updateRequest = {
            updated: [],
            active: []
        };

        changed = ko.observable(false);

        housingUnits = ko.observableArray<IHousingUnit>();
        housingTypes = ko.observableArray<ILookupItem>();
        unitForm = {
            heading: ko.observable(),
            unitId: ko.observable(0),
            unitname: ko.observable<string>(),
            capacity: ko.observable<string>(),
            selectedHousingType: ko.observable<ILookupItem>(),
            errorMessage: ko.observable<string>(),
            active: ko.observable(true)
        };

        public constructor(application:IPeanutClient, owner:IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public getUnits() {
            var me = this;
            var request = {
                units: 'all',
                types: 'none'
            };
            if (me.housingTypes().length == 0) {
                request.types = 'all';
            }
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting housing units...');

             me.peanut.executeService('registration.GetHousingUnits', request,
                 function(serviceResponse:IServiceResponse) {
                     if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                         var response = <IGetHousingUnitsResponse>serviceResponse.Value;
                         me.updateRequest.active = [];
                         me.updateRequest.updated = [];
                         me.housingUnits(response.units);
                         if (response.types) {
                             me.housingTypes(response.types);
                         }
                     }
                 }
             )
             .always(function () {
                me.application.hideWaiter();
             });
        }

        public getTypes() {
            var me = this;
            var request = 'all';
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting housing types...');

            me.peanut.executeService('registration.GetHousingTypes', request,
                    function(serviceResponse:IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            var response = <ILookupItem[]>serviceResponse.Value;
                            me.housingTypes(response);
                        }
                    }
                )
                .always(function () {
                    me.application.hideWaiter();
                });
        }
        private handleGetUnitsResponse = (serviceResponse:IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess || serviceResponse.Result == Peanut.serviceResultWarnings) {
                var response = <IGetHousingUnitsResponse>serviceResponse.Value;
                if (me.housingUnits().length > 0) {
                    me.notifyUnitsUpdate(response.units);
                }
                me.housingUnits(response.units);
                if (response.types) {
                    me.housingTypes(response.types);
                }
                me.refreshNeeded = false;
                me.updateRequest.active = [];
                me.updateRequest.updated = [];
                me.changed(false);
            }
        };

        toggleActive = (selectedUnit:IHousingUnit) => {
            var me = this;
            if (selectedUnit != null) {
                var newValue = (selectedUnit.active);
                me.changed(true);
                var existing = _.find(me.updateRequest.active, function (item:any) {
                    return item.unitname == selectedUnit.unitname;
                });
                if (existing == null) {
                    me.updateRequest.active.push(
                        {
                            id: selectedUnit.housingUnitId,
                            active: newValue
                        }
                    );
                }
                else {
                    existing.active = newValue;
                }
            }

            return true; // required to keep click event from re-checking the checkbox
        };


        saveChanges = () => {
            var me = this;
            var request = me.updateRequest;
            me.application.showWaiter('Saving changes...');
            me.peanut.executeService('registration.UpdateHousingUnits', me.updateRequest, me.handleGetUnitsResponse)
                .always(function () {
                    me.application.hideWaiter();
                });
        };

        showEditUnitDialog = (unit:IHousingUnit) => {
            var me = this;
            me.unitForm.heading('Update Housing Unit');
            me.unitForm.unitId(unit.housingUnitId);
            me.unitForm.unitname(unit.unitname);
            me.unitForm.capacity(unit.capacity.toString());
            me.unitForm.selectedHousingType(me.getHousingType(unit.housingTypeId));
            me.unitForm.active(unit.active);
            me.unitForm.errorMessage('');
            me.showForm();
        };

        showNewUnitDialog = () => {
            var me = this;
            me.unitForm.heading('New Housing Unit');
            me.unitForm.unitId(0);
            me.unitForm.unitname('');
            me.unitForm.capacity('2');
            me.unitForm.selectedHousingType(null);
            me.unitForm.errorMessage('');
            me.unitForm.active(true);
            me.showForm();
        };


        updateUnit = () => {
            var me = this;
            var request = me.validateDialog();
            if (request == null) {
                return;
            }
            me.changed(true);
            me.assignToUpdateList(me.updateRequest.updated,request);
            me.updateDisplayList(request);
        };

        private updateDisplayList(request: IHousingUnitUpdateRequest) {
            var me = this;
            var list = me.housingUnits();
            var existing : IHousingUnit = _.find(list,function(unit: IHousingUnit){
                return request.unitname == unit.unitname;
            });
            if (existing == null) {
                existing = {
                    active : me.unitForm.active(),
                    capacity: request.capacity,
                    categoryName: '',
                    description: request.unitname,
                    housingCategoryId: null,
                    housingTypeId: request.housingTypeId,
                    housingTypeName: me.unitForm.selectedHousingType().Text,
                    housingUnitId: request.unitId,
                    unitname: request.unitname
                };
                list.push(existing);
            }
            else {
                existing.capacity = request.capacity;
                existing.housingTypeId = request.housingTypeId;
                existing.housingTypeName = me.unitForm.selectedHousingType().Text;
            }
            me.housingUnits([]);
            me.housingUnits(_.sortBy(list,'unitname'));
        }

        private assignToUpdateList(list: IHousingUnitUpdateRequest[], item: IHousingUnitUpdateRequest) {
            var me = this;
            var existing = _.find(list, function(listItem: IHousingUnitUpdateRequest){
                return listItem.unitname == item.unitname;
            });
            if (existing == null) {
                list.push(item);
            }
            else {
                existing.capacity = item.capacity;
                existing.housingTypeId = item.housingTypeId;
            }
        }

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

        notifyUnitsUpdate = (data:any) => {
            var me = this;
            if (me.owner) {
                me.owner.handleEvent('housingunitsupdated', data);
            }
        };

        handleEvent = (eventName:string, data:any = null)=> {
            var me = this;
            switch (eventName) {
                case 'housingtypesupdated' :
                    me.refreshNeeded = true;
                    break;
                case 'unitspageselected' :
                    var changed = me.changed();
                    if (me.refreshNeeded && !me.changed()) {
                        me.getUnits();
                    }
                    break;
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
            var request:IHousingUnitUpdateRequest = {
                unitId: me.unitForm.unitId(),
                unitname: me.unitForm.unitname().trim(),
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
    }

}
