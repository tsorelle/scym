/**
 * Created by Terry on 10/30/2015.
 */
// replace all occurances of 'ComponentTest' with the name of your model

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='Registration.d.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class ComponentTestViewModel implements IHousingViewModel, IMainViewModel {
        handleEvent:(eventName:string, data:any)=>void;
        getAssignments:(registrationId:number)=>void;
        reset:()=>void;
        initialize:(finalFunction?:()=>void)=>void;
        static instance: Tops.ComponentTestViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        housingTypes = ko.observableArray<ILookupItem>();
        housingUnits = ko.observableArray<IHousingUnit>();
        housingUnitList : IHousingUnit[];
        housingAssignments = ko.observableArray<IAttenderHousingAssignment>();

        defaultHousingUnit = ko.observable<IHousingUnit>();
        defaultHousingType = ko.observable<ILookupItem>();
        assignedByAttender = ko.observable(false);

        updatedAssignments : IHousingAssignmentUpdate[] = [];

        // Constructor
        constructor() {
            var me = this;
            Tops.ComponentTestViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;

        }

        private foo = false;


        /**
         * @param applicationPath - root path of application or location of service script
         * @param successFunction - page inittializations such as ko.applyBindings() go here.
         *
         * Call this function in a script at the end of the page following the closing "body" tag.
         * e.g.
         *      ViewModel.init('/', function() {
         *          ko.applyBindings(ViewModel);
         *      });
         *
         */
        init(applicationPath: string = '/', successFunction?: () => void) {
            var me = this;
            // setup messaging and other application initializations
            // initialize date popus if used
            /*
             jQuery(function() {
             jQuery( ".datepicker" ).datepicker();
             });
             */

            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.
                    me.application.hideServiceMessages();
                    me.application.showWaiter('Getting assignments...');

                    // fake *****************
                    me.handleGetAssignmentsResponse(
                        me.getFakeResponse()
                    );
                    me.application.hideWaiter();

                    /*
                     var request = 1;
                    me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
                        .always(function() {
                            me.application.hideWaiter();
                        });
                   */

                }
            );
        }

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

        private handleGetAssignmentsResponse = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetHousingAssignmentsResponse>serviceResponse.Value;
                me.housingTypes(response.housingTypes);
                me.housingUnitList = response.units;
                me.housingUnits(me.housingUnitList);
                me.showAssignments(response.assignments);
                me.application.loadComponent('housing-selector',function() {
                    me.application.bindSection('testview',me);
                    // me.application.bindSection('buttons',me);
                });
            }
        };


        getHousingUnit = (id: number) => {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find( me.housingUnits(), function (unit:IHousingUnit) {
                    return unit.housingUnitId == id;
                }, me);
            }
            return selected;
        };

        getHousingType = (id: number) => {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find( me.housingTypes(), function (item:ILookupItem) {
                    return item.Key == id;
                }, me);
            }
            return selected;
        };

        getHousingUnitList = (typeId: number = 0) => {
            var me = this;
            if (typeId) {
                var filtered = _.filter(me.housingUnitList,function(unit: IHousingUnit) {
                    return (
                        (unit && unit.housingTypeId == typeId)
                    );
                },me);
                return filtered;
            }
            else {
                return me.housingUnitList;
            }
        };

        onTypeChange = (selected : ILookupItem) => {
            var me = this;
            var id = 0;
            if (selected) {
                id = selected.Key;
            }
            me.filterHousingUnitList(id);
        };


        private filterHousingUnitList = (typeId: number) => {
            var me = this;
            var filtered = me.getHousingUnitList(typeId);
            me.housingUnits(filtered);
            me.defaultHousingUnit(null);
        };



        showAssignments(attenderAssignments : IAttenderHousingAssignment[]) {
            var me = this;
            me.housingAssignments(attenderAssignments);

            var defaultAssignment : IHousingAssignment = null;
            var defaultType : number = null;
            var defaultUnit : number = null;
            var showDetail = false;

            if (attenderAssignments.length) {
                var first : IAttenderHousingAssignment = me.findFirst(attenderAssignments);
                if (first) {
                    defaultType = first.attender.housingPreference;
                    defaultAssignment = me.findFirst(first.assignments);
                    if (defaultAssignment) {
                        var nonMatchAttender =  _.find( attenderAssignments,
                            function(attenderAssignment: IAttenderHousingAssignment) {
                                var nonMatch = _.find(attenderAssignment.assignments,
                                    function(assignment: IHousingAssignment) {
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
                    };
                }
            }

            var housingType = me.getHousingType(defaultType);
            me.defaultHousingType.subscribe(me.onTypeChange);
            me.defaultHousingType(housingType);

            if (defaultAssignment) {
                var unit = me.getHousingUnit(defaultAssignment.housingUnitId);
                me.defaultHousingUnit(unit);
            }



            me.assignedByAttender(showDetail);
        }

        findFirst(items: any[]) {
            var first = _.find(items,function(item: any) {
                return true;
            });
            return first;
        }

        showAssignmentDetail = () => {
            var me = this;
            me.assignedByAttender(true);
        };

        updateAssignment = (attenderId: number, assignment: IHousingAssignment) => {
            var me = this;
            if (!(attenderId in me.updatedAssignments)) {
                me.updatedAssignments[attenderId] =  {
                    attenderId: attenderId,
                    assignments: []
                }
            }
            me.updatedAssignments[attenderId].assignments[assignment.day] = assignment;
        };

        test = () => {
            var me = this;
            var result = me.updatedAssignments;
        };

        public serviceCallTemplate() {
            // todo: delete serviceCallTemplate when not needed
            var me = this;
            var request = null; //

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');
            me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private handleServiceResponseTemplate = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {


            }
        };

    }
}

Tops.ComponentTestViewModel.instance = new Tops.ComponentTestViewModel();
(<any>window).ViewModel = Tops.ComponentTestViewModel.instance;
Tops.ComponentTestViewModel.instance.init();