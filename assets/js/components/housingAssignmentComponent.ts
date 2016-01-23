/**
 * Created by Terry on 12/18/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class housingAssignmentComponent implements IHousingViewModel, IRegistrationComponent {

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        private refreshNeeded = false;

        registrationId = ko.observable<any>();
        housingTypes = ko.observableArray<ILookupItem>();
        housingUnits = ko.observableArray<IHousingUnit>();
        housingUnitList:IHousingUnit[] = [];
        housingUnitsDaily: IHousingUnit[][];
        housingAssignments = ko.observableArray<IAttenderHousingAssignment>();
        housingAvailatility : IHousingAvailabilityItem[];

        defaultHousingUnit = ko.observable<IHousingUnit>();
        defaultHousingType = ko.observable<ILookupItem>();
        defaultHousingTypeSubscription: any;
        defaultHousingUnitSubscription: any;
        assignedByAttender = ko.observable(false);
        confirmationText = ko.observable();
        canClose = ko.observable(true);

        formTitle = ko.observable();
        showFormTitle = true;

        updatedAssignments = ko.observableArray<IHousingAssignmentsUpdate>();

        public constructor(application:IPeanutClient, owner: IEventSubscriber, showFormTitle = true) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.showFormTitle = showFormTitle;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.assignedByAttender.subscribe(me.onShowAttenders);
            me.application.loadComponent('housing-selector',function() {
                if (finalFunction) {
                    finalFunction();
                }
            });
        }

        public hideCloseButton = () => {
            this.canClose(false);
        };

        public getAssignments(registrationId:number) {
            var me = this;

            me.housingAssignments([]);
            me.formTitle('');
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting assignments...');

            var request =
            {
                registrationId: registrationId,
                includeLookups: me.housingTypes().length == 0 || me.housingUnitList.length == 0
            };

             me.peanut.executeService('registration.GetHousingAssignments',request, me.handleGetAssignmentsResponse)
             .always(function() {
                me.application.hideWaiter();
             });
        }

        private handleGetAssignmentsResponse = (serviceResponse:IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetHousingAssignmentsResponse>serviceResponse.Value;
                me.registrationId(response.registrationId);
                if(response.housingTypes) {
                    me.housingTypes(response.housingTypes);
                }

                if (response.units) {
                    me.housingUnitsDaily = [];
                    me.housingAvailatility = response.availability;
                    me.housingUnitList = me.buildHousingUnitList(0, response.units);
                    me.housingUnits(me.housingUnitList);
                }

                me.showAssignments(response.assignments);
                if (me.showFormTitle) {
                    me.formTitle("Housing assignments for " + response.registrationName);
                }
                me.owner.handleEvent('houingassignmentsloaded',response.registrationId);
            }
        };

        reset = () => {
            var me = this;
            me.disableDefaultSubscriptions();
            me.housingAssignments([]);
            me.assignedByAttender(false);
        };

        buildHousingUnitList = (day: number = 0, sourceList : IHousingUnit[] = null) => {
            var me = this;
            var result = [];
            var availablityList;
            if (sourceList == null) {
                sourceList = me.housingUnitList;
            }
            if (day == 0) {
                availablityList = me.housingAvailatility;
            }
            else
            {
                availablityList = _.filter(me.housingAvailatility,function(item: IHousingAvailabilityItem){
                   return item.day == day;
                });
            }
            _.each(sourceList,function(unit: IHousingUnit){

                var availability : IHousingAvailabilityItem;
                var occupants = 0;
                if (day == 0) {
                    var items = _.filter(availablityList, function (a:IHousingAvailabilityItem) {
                        return (a.housingUnitId == unit.housingUnitId);
                    });

                    if (items.length == 0) {
                        availability = null;
                    }
                    else {
                        availability = _.max(items, function (i:IHousingAvailabilityItem) {
                            return i.occupants;
                        });
                    }
                }
                else {
                    availability = _.find(availablityList,function(a : IHousingAvailabilityItem) {
                        return (a.day == day && a.housingUnitId == unit.housingUnitId);
                    });
                }
                
                if (availability != null) {
                    occupants = availability.occupants;
                }
                var description = unit.unitname + ' (';
                if (occupants == unit.capacity) {
                    description += 'Full';
                }
                else if (occupants > unit.capacity) {
                    description += 'Overbooked';
                }
                else if (occupants == 0) {
                    description += unit.capacity + ' available';
                }
                else {
                    description += (unit.capacity - occupants) + ' left of ' + unit.capacity;
                }
                result.push({
                    housingTypeId: unit.housingTypeId,
                    housingUnitId: unit.housingUnitId,
                    unitname: unit.unitname,
                    housingTypeName: unit.housingTypeName,
                    housingCategoryId: unit.housingCategoryId,
                    categoryName: unit.housingTypeName,
                    capacity: unit.capacity,
                    description: description + ')'
                });
            });
            return result;
        };

        getHousingUnit = (id:number, unitList : IHousingUnit[] = null) => {
            var me = this;
            var selected = null;
            if (unitList == null) {
                unitList = me.housingUnits();
            }
            if (id > 0) {
                selected = _.find(unitList, function (unit:IHousingUnit) {
                    return unit.housingUnitId == id;
                }, me);
            }
            return selected;
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

        getHousingUnitList = (typeId:number = 0, day: number = 0) => {
            var me = this;
            var unitList = me.housingUnitList;
            if (day > 0 && me.housingUnitsDaily.length > 0) {
                unitList = me.housingUnitsDaily[day - 4];
            }
            if (typeId) {
                var filtered = _.filter(unitList, function (unit:IHousingUnit) {
                    return (
                        (unit && unit.housingTypeId == typeId)
                    );
                }, me);
                return filtered;
            }
            else {
                return unitList;
            }
        };

        onTypeChange = (selected:ILookupItem) => {
            var me = this;
            var id = 0;
            if (selected) {
                id = selected.Key;
            }
            me.filterHousingUnitList(id);
        };

        onDefaultUnitChange = (selected: IHousingUnit) => {
            var me = this;
            var housingUnitId = 0;
            if (selected) {
                housingUnitId = selected.housingUnitId;
            }
            var wildcardAssignment:IHousingAssignmentsUpdate = {
                attenderId: 0,
                assignments: [
                    {
                        day: 0,
                        housingUnitId: housingUnitId,
                        note: ''
                    }
                ]
            };
            me.updatedAssignments([wildcardAssignment]);

        };

        private filterHousingUnitList = (typeId:number) => {
            var me = this;
            var filtered = me.getHousingUnitList(typeId);
            me.housingUnits(filtered);
            me.defaultHousingUnit(null);
        };

        disableDefaultSubscriptions = () => {
            var me = this;
            if (me.defaultHousingTypeSubscription != null) {
                me.defaultHousingTypeSubscription.dispose();
                me.defaultHousingTypeSubscription = null;
            }
            if (me.defaultHousingUnitSubscription != null) {
                me.defaultHousingUnitSubscription.dispose();
                me.defaultHousingUnitSubscription = null;
            }

        };

        showAssignments(attenderAssignments:IAttenderHousingAssignment[]) {
            var me = this;

            var defaultAssignment:IHousingAssignment = null;
            var defaultType:number = null;
            var defaultUnit:number = null;
            var showDetail = false;

            me.disableDefaultSubscriptions();

            if (attenderAssignments.length) {
                var first:IAttenderHousingAssignment = me.findFirst(attenderAssignments);
                if (first) {
                    defaultType = first.attender.housingPreference;
                    defaultAssignment = me.findFirst(first.assignments);
                    if (defaultAssignment) {
                        var nonMatchAttender = _.find(attenderAssignments,
                            function (attenderAssignment:IAttenderHousingAssignment) {
                                var nonMatch = _.find(attenderAssignment.assignments,
                                    function (assignment:IHousingAssignment) {
                                        return assignment.housingUnitId != defaultAssignment.housingUnitId;
                                    }
                                );
                                return nonMatch != null;
                            }
                        );
                        if (nonMatchAttender) {
                            defaultAssignment = null;
                            showDetail = true;
                        }
                    }
                }
            }

            if (!showDetail) {
                var housingType = me.getHousingType(defaultType);
                me.defaultHousingType(housingType);
                var unit = null;
                if (defaultAssignment) {
                    unit = me.getHousingUnit(defaultAssignment.housingUnitId);
                }
                me.defaultHousingUnit(unit);

                me.defaultHousingTypeSubscription = me.defaultHousingType.subscribe(me.onTypeChange);
                me.defaultHousingUnitSubscription = me.defaultHousingUnit.subscribe(me.onDefaultUnitChange);
            }

            me.housingAssignments(attenderAssignments);
            me.assignedByAttender(showDetail);
        }

        onShowAttenders = (visible: boolean) => {
            var me = this;
            if (visible && me.housingUnitsDaily.length == 0) {
                for(var day = 4; day < 7; day += 1) {
                    me.housingUnitsDaily[day - 4] = me.buildHousingUnitList(day,me.housingUnitList);
                }
            }
        };

        findFirst(items:any[]) {
            var first = _.find(items, function (item:any) {
                return true;
            });
            return first;
        }

        showAssignmentDetail = () => {
            var me = this;
            me.updatedAssignments([]);
            me.assignedByAttender(true);
        };

        updateAssignment = (attenderId:number, assignment: IHousingAssignment) => {
            var me = this;
            var updates = me.updatedAssignments();
            var update : IHousingAssignmentsUpdate = _.find(updates, function(item: IHousingAssignmentsUpdate) {
                return item.attenderId == attenderId;
            });

            if (update == null) {
                update = {
                    attenderId: attenderId,
                    assignments: [assignment]
                };
                updates.push(update);
            }
            else {
                var existing = _.find(update.assignments,function(assignmentItem: IHousingAssignment){
                    return assignmentItem.day == assignment.day;
                });
                if (existing != null) {
                    existing.housingUnitId = assignment.housingUnitId;
                    existing.note = assignment.note;
                }
                else {
                    update.assignments.push(assignment);
                }
            }

            me.updatedAssignments(updates);
        };

        saveAssignments = () => {
            var me = this;
            var request : IHousingAssignmentUpdateRequest = {
                registrationId: me.registrationId(),
                updates: []
            };

            if (me.assignedByAttender()) {
                request.updates = me.updatedAssignments();
            }
            else {
                var defaultUnitId = me.defaultHousingUnit().housingUnitId;
                var assignments = me.housingAssignments();
                _.each(assignments, function(attenderAssignment : IAttenderHousingAssignment) {
                    // attenderAssignment.assignments = [];
                    _.each(attenderAssignment.assignments, function($a : IHousingAssignment) {
                        $a.housingUnitId = defaultUnitId;
                    });
                    request.updates.push(
                        {
                            attenderId: attenderAssignment.attender.attenderId,
                            assignments: attenderAssignment.assignments
                        }
                    );
                });
            }

            if (request.updates.length > 0) {
                me.application.hideServiceMessages();
                me.application.showWaiter('Updating...');
                me.peanut.executeService('registration.UpdateHousingAssignments', request, me.handleSaveAssingmentsResponse)
                    .always(function () {
                        me.application.hideWaiter();
                    });
            }
        };

        private handleSaveAssingmentsResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.updatedAssignments([]);
            }
        };

        closeForm = () => {
            var me = this;
            me.reset();
            if (me.owner) {
                me.owner.handleEvent('assignmentformclosed');
            }
        };

        reloadPage = () => {
            var me = this;
            window.location.reload(true);
            // jQuery("#confirm-reload-modal").modal('hide');
        };

        onPageSelected = () => {
            var me = this;
            if (me.refreshNeeded) {
                me.refreshNeeded = false;
                if (me.housingTypes().length + me.housingUnitList.length > 0) {
                    jQuery("#confirm-reload-modal").modal('show');
                    // me.refreshHousingUnits();
                }
            }
        };

        handleEvent = (eventName:string, data:any = null)=> {
            var me = this;
            switch (eventName) {
                case 'housingtypesupdated':
                    me.refreshNeeded = true;
                    break;
                case 'housingunitsupdated':
                    me.refreshNeeded = true;
                    break;
                case 'assignmentspageselected' :
                    me.onPageSelected();
                    break;
            }
        };

        refreshHousingUnits = () => {
            // not needed?
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            // me.application.showWaiter('Message here...');

            // fake
            var response = null;
            me.handleRefreshHousingUnits(response);
            // me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        };

        private handleRefreshHousingUnits = (serviceResponse: IServiceResponse) => {
            // not needed?
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IRefreshHousingUnitsResponse>serviceResponse.Value;
                me.housingUnitList = response.units;
                me.housingAvailatility = response.availability;
                me.housingUnitList = me.buildHousingUnitList(0, response.units);
                me.housingUnits(me.housingUnitList);
                me.housingUnitsDaily = [];
                me.onShowAttenders(me.assignedByAttender());
            }
        };

        getConfirmationText = () => {
            // todo: getConfirmationText
            var me = this;
            var request = me.registrationId();

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');

             me.peanut.executeService('registration.GetHousingConfirmationText',request, me.handleGetConfirmationTextResponse)
             .always(function() {
                me.application.hideWaiter();
             });

        };

        private handleGetConfirmationTextResponse = (serviceResponse: IServiceResponse) => {
            // todo: handleGetConfirmation text
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.confirmationText(serviceResponse.Value);
                jQuery("#confirmation-message-modal").modal('show');

            }
        };

        sendConfirmationMessage = () => {
            // todo: sendConfirmationMessage
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Sending message...');

            // fake
            var response = new fakeServiceResponse(null);
            me.handleSendConfirmationResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        };

        private handleSendConfirmationResponse = (serviceResponse: IServiceResponse) => {
            // todo:handleSendConfirmationResponse
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                jQuery("#confirmation-message-modal").modal('hide');
                me.closeForm();
            }
        };



        /********** Fakes ***************/

        private getFakeConfirmationText() {
            var faketext =
                "Dear Friend,\n" +
                "\n" +
                "Thank you for registering for SCYM.  We look forward to seeing you there.\n" +
                "When you arrive at camp, enter this code to open the gate: #7296 (or, on a phone keypad it's #SCYM)\n" +
                "At this time, your fee for the event is $0 Payment must be either check or cash as SCYM does not accept credit cards.\n" +
                "\n" +
                "Your room assignments are as follows:\n" +
                "\n" +
                "Rex:\n" +
                "    Thursday: Motel 30 (Camp Motel)\n" +
                "    Friday: Motel 30 (Camp Motel)\n" +
                "    Saturday: Motel 30 (Camp Motel)\n" +
                "\n" +
                "Maggie:\n" +
                "    Thursday: Motel 29 (Camp Motel)\n" +
                "    Friday: Motel 29 (Camp Motel)\n" +
                "    Saturday: Motel 29 (Camp Motel)\n" +
                "\n" +
                "Sisca:\n" +
                "    Thursday: Motel 30 (Camp Motel)\n" +
                "    Friday: Motel 30 (Camp Motel)\n" +
                "    Saturday: Motel 30 (Camp Motel)\n" +
                "\n" +
                "Check in at registration in the Dining Hall when you get to Greene Family Camp to be sure nothing has changed and to complete your registration before you go to your lodging.\n" +
                "\n" +
                "If you have any questions, please contact me at registrar@scym.org. Starting thursday morning of Yearly Meeting, you may reach me at (512) 913-5434 if you have questions or concerns.\n" +
                "\n" +
                "Galia Harrington\n";
            return new fakeServiceResponse(faketext);
        }

        /*******************************/
    }
}
