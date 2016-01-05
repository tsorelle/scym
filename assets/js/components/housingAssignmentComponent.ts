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
        housingAssignments = ko.observableArray<IAttenderHousingAssignment>();

        defaultHousingUnit = ko.observable<IHousingUnit>();
        defaultHousingType = ko.observable<ILookupItem>();
        defaultHousingTypeSubscription: any;
        defaultHousingUnitSubscription: any;
        assignedByAttender = ko.observable(false);
        confirmationText = ko.observable();

        formTitle = ko.observable();
        showFormTitle = true;

        updatedAssignments = ko.observableArray<IHousingAssignmentUpdate>();

        public constructor(application:IPeanutClient, owner: IEventSubscriber, showFormTitle = true) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.showFormTitle = showFormTitle;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.application.loadComponent('housing-selector',function() {
                if (finalFunction) {
                    finalFunction();
                }
            });
        }

        public getAssignments(registrationId:number) {
            // todo: getAssignments
            var me = this;

            me.housingAssignments([]);
            me.formTitle('');
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting assignments...');

            var request = registrationId;

            // fake
            var response = me.getFakeResponse();
            me.handleGetAssignmentsResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('registration.GetAssignments',request, me.handleServiceResponseTemplate)
             .always(function() {
             me.application.hideWaiter();
             });
             */
        }

        private handleGetAssignmentsResponse = (serviceResponse:IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetHousingAssignmentsResponse>serviceResponse.Value;
                me.registrationId(response.registrationId);
                me.housingTypes(response.housingTypes);
                me.housingUnitList = response.units;
                me.housingUnits(me.housingUnitList);
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


        getHousingUnit = (id:number) => {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find(me.housingUnits(), function (unit:IHousingUnit) {
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

        getHousingUnitList = (typeId:number = 0) => {
            var me = this;
            if (typeId) {
                var filtered = _.filter(me.housingUnitList, function (unit:IHousingUnit) {
                    return (
                        (unit && unit.housingTypeId == typeId)
                    );
                }, me);
                return filtered;
            }
            else {
                return me.housingUnitList;
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
            var wildcardAssignment:IHousingAssignmentUpdate = {
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

        updateAssignment = (attenderId:number, assignment:IHousingAssignment) => {
            var me = this;
            var updates = me.updatedAssignments();
            if (!(attenderId in updates)) {
                updates[attenderId] = {
                    attenderId: attenderId,
                    assignments: []
                }
            }
            updates[attenderId].assignments[assignment.day] = assignment;
            me.updatedAssignments(updates);
        };

        saveAssignments = () => {
            // todo: saveAssignments
            var me = this;
            var request : IHousingAssignmentUpdateRequest = {
                registrationId: me.registrationId(),
                updates: me.updatedAssignments()
            };

            if (request.updates.length > 0) {
                me.application.hideServiceMessages();
                me.application.showWaiter('Updating...');
                // fake
                me.handleSaveAssingmentsResponse(new fakeServiceResponse(null));
                me.application.hideWaiter();

                /*
                me.peanut.executeService('registration.UpdateHousingAssignments', request, me.handleSaveAssingmentsResponse)
                    .always(function () {
                        me.application.hideWaiter();
                    });
                */
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
            window.location.reload(true);
            // jQuery("#confirm-reload-modal").modal('hide');
        };

        onPageSelected = () => {
            var me = this;
            if (me.refreshNeeded) {
                me.refreshNeeded = false;
                if (me.housingTypes().length + me.housingUnitList.length > 0) {
                    jQuery("#confirm-reload-modal").modal('show');
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
            // todo: refreshHousingUnits()
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
             // me.application.hideWaiter();
             });
             */
        };

        private handleRefreshHousingUnits = (serviceResponse: IServiceResponse) => {
            // todo: handleRefreshHousingUnits
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.housingUnitList = <IHousingUnit[]>serviceResponse.Value;


            }
        };

        getConfirmationText = () => {
            // todo: getConfirmationText
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');

            // fake
            var response = me.getFakeConfirmationText();
            me.handleGetConfirmationTextResponse(response);
            me.application.hideWaiter();

            /*
             me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
             .always(function() {
             me.application.hideWaiter();
             });
             */

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
        private getFakeResponse() {
            var fakeHousingTypes : ILookupItem[] = [
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

            var fakeUnits : IHousingUnit[] = [
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
            var fakeAssignments : IAttenderHousingAssignment[] = [
                {
                    attender : {
                        attenderId : 1,
                        attenderName: 'Terry SoRelle',
                        housingPreference: 3
                    },

                    assignments: [
                        {
                            day: 5,
                            housingUnitId: 1,
                            note: '',
                        },
                        {
                            day: 6,
                            housingUnitId: 1,
                            note: '',
                        }
                    ]
                },
                {
                    attender: {
                        attenderId: 2,
                        attenderName: 'Liz Yeats',
                        housingPreference: 3,
                    },
                    assignments: [
                        {
                            day: 5,
                            housingUnitId: 1,
                            note: '',
                        },
                        {
                            day: 6,
                            housingUnitId: 1,
                            note: '',
                        }
                    ]
                }

            ] ;

            var responseData : IGetHousingAssignmentsResponse = {
                registrationId: 1,
                registrationName: 'Terry and Liz',
                units: fakeUnits,
                assignments: fakeAssignments,
                housingTypes: fakeHousingTypes
            };
            return new fakeServiceResponse(responseData);
        }

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
