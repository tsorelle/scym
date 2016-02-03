/**
 * Created by Terry on 12/18/2015.
 */

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../components/housingAssignmentComponent.ts' />
///<reference path="../components/housingTypesComponent.ts"/>
///<reference path="../components/housingUnitsComponent.ts"/>
///<reference path="../components/housingLookupComponent.ts"/>
///<reference path="../components/housingReportsComponent.ts"/>

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class HousingManagementViewModel implements IMainViewModel, IEventSubscriber {

        // todo: housing reports page

        static instance: Tops.HousingManagementViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        currentForm = ko.observable('assignments');

        private housingAssignmentsVM : IHousingViewModel;
        private housingTypesVm: any;
        private housingUnitsVm: any;
        private housingLookupVm: any;
        private housingReportsVm: any;

        // Constructor
        constructor() {
            var me = this;
            Tops.HousingManagementViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        private bindSection = (containerName: string, context? : any) => {
            var me = this;
            if (!context) {
                context = me;
            }
            me.application.bindSection(containerName,context);
        };

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
            successFunction = me.afterInit;
            me.application.initialize(applicationPath,
                function () {
                    me.application.loadResources(['housingAssignmentComponent.js', 'housingLookupComponent.js','searchListObservable.js'],
                        function () {
                            me.housingAssignmentsVM = new housingAssignmentComponent(me.application,me);
                            me.application.registerComponent('housing-assignment',me.housingAssignmentsVM,function() {
                                me.housingAssignmentsVM.initialize(function() {
                                    me.housingLookupVm = new housingLookupComponent(me.application,me); // .onRegistrationSelected);
                                    me.application.registerComponent('housing-lookup',me.housingLookupVm,
                                        function () {
                                            me.application.loadComponent('modal-confirm', function() {
                                                successFunction();
                                            });
                                        });
                                });
                            });
                        });
                });
        }

        private loadAssignmentsForm() {
            var me = this;

        }

        private loadLookupForm() {
            var me = this;

        }

        afterInit = () => {
            var me = this;

            me.application.bindSection('tabs');
            me.application.bindSection('assignments');
            me.application.bindNode('housing-units-form');
            me.application.bindNode('housing-types-form');
            me.application.bindNode('housing-reports-form');
            me.application.showDefaultSection();
        };

        onRegistrationSelected = (regId: any) => {
            var me = this;
            if (regId) {
                me.housingLookupVm.searchFormVisible(false);
                me.housingAssignmentsVM.getAssignments(regId);
            }
            else {
                me.housingAssignmentsVM.reset();
            }
        };

        showAssignmentForm = () => {
            var me = this;
            me.currentForm('assignments');
            me.housingAssignmentsVM.handleEvent('assignmentspageselected');
        };


        showHousingUnitsForm = () => {
            var me = this;
            if (me.housingUnitsVm) {
                me.currentForm('units');
                me.housingUnitsVm.handleEvent('unitspageselected');
            }
            else {
                me.application.bindComponent('housing-units',
                    function() {
                        me.housingUnitsVm = new housingUnitsComponent(me.application,me);
                        return me.housingUnitsVm;
                    },
                    function() {
                        me.housingUnitsVm.getUnits();
                        me.currentForm('units');
                    }
                );
            }
        };

        showHousingTypesForm = () => {
            var me = this;
            if (me.housingTypesVm) {
                me.currentForm('types');
            }
            else {
                me.application.bindComponent('housing-types',
                    function() {
                        me.housingTypesVm = new housingTypesComponent(me.application, me);
                        return me.housingTypesVm;
                    },
                    function() {
                        me.housingTypesVm.getTypes();
                        me.currentForm('types');
                    }
                );
            }
        };

        showHousingReportsForm = () => {
            var me = this;
            if (me.housingReportsVm) {
                me.currentForm('reports');
            }
            else {
                me.application.bindComponent('housing-reports',
                    function() {
                        me.housingReportsVm = new housingReportsComponent(me.application, me);
                        return me.housingReportsVm;
                    },
                    function() {
                        me.housingReportsVm.initialize();
                        me.currentForm('reports');
                    }
                );
            }
        };

        private housingTypesToLookupList(data: any) {
            var result = [];
            var types : IHousingType[] = _.sortBy(<IHousingType[]>data,'housingTypeDescription');
            _.each(types,function(type: IHousingType) {
                var item : ILookupItem = {
                    Key: type.housingTypeId,
                    Text: type.housingTypeDescription,
                    Description: ''
                };
                result.push(item);
            });
        }

        handleEvent = (eventName:string, data:any = null) => {
            var me = this;
            switch (eventName) {
                case 'housingtypesupdated':
                    var typesList = me.housingTypesToLookupList(data);
                    if (me.housingUnitsVm) {
                        me.housingUnitsVm.handleEvent(eventName,typesList);
                    }
                    if (me.housingTypesVm) {
                        me.housingAssignmentsVM.handleEvent(eventName, typesList);
                    }
                    break;
                case 'housingunitsupdated':
                    me.housingAssignmentsVM.handleEvent(eventName,data);
                    break;
                case 'assignmentformclosed' :
                    me.housingLookupVm.searchFormVisible(true);
                    break;
                case 'registrationselected' :
                    me.onRegistrationSelected(data);
                    break;
                case 'assignmentchangerequest' :
                    me.showAssignmentForm();
                    me.onRegistrationSelected(data.registrationId);
                    break;

            }
        }
    }
}

// (new Tops.HousingManagementViewModel()).init();
Tops.HousingManagementViewModel.instance = new Tops.HousingManagementViewModel();
(<any>window).ViewModel = Tops.HousingManagementViewModel.instance;
