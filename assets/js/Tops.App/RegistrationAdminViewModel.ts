/**
 * Created by Terry on 1/4/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="registration.d.ts"/>
/// <reference path='../components/housingAssignmentComponent.ts' />
/// <reference path='../components/registrationLookupComponent.ts' />
/// <reference path='../components/registrationFinanceComponent.ts' />
/// <reference path='../components/adminReportsComponent.ts' />
/// <reference path='../components/registrationDashboardComponent.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class RegistrationAdminViewModel implements IMainViewModel, IRegistrationHost {
        static instance: Tops.RegistrationAdminViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;
        private context : IRegistrationContext = null;

        currentForm = ko.observable('registrations');
        counts = {
            registrations: ko.observable(''),
            attenders: ko.observable('')
        };
        selectedRegistrationId = ko.observable(0);

        private registrationLookupVm: any;
        private registrationDashboardVm: any;
        private registrationFinanceVm: any;
        private adminReportsVm: any;
        private housingAssignmentsVM : any; //IHousingViewModel;

        private registrationChanged = false;

        // Constructor
        constructor() {
            var me = this;
            Tops.RegistrationAdminViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }


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
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            // setup messaging and other application initializations
            // initialize date popus if used
            /*
             jQuery(function() {
             jQuery( ".datepicker" ).datepicker();
             });
             */
            jQuery('.page-header').addClass('hidden-print');

            // todo: refactor to load on tab click
            successFunction = me.afterInit;
            me.application.initialize(applicationPath,
                function () {
                    me.application.loadResources([
                            'registrationLookupComponent.js',
                            'registrationDashboardComponent.js',

                            // 'housingAssignmentComponent.js',
                            // 'registrationFinanceComponent.js',
                            // 'adminReportsComponent.js'
                            ],
                        function () {
                            me.registrationLookupVm = new registrationLookupComponent(me.application, me);
                            me.application.registerComponent('registration-lookup', me.registrationLookupVm,
                                function () {
                                    me.registrationLookupVm.initialize(function(){
                                        me.registrationDashboardVm = new registrationDashboardComponent(me.application,me);
                                        me.application.registerComponent('registration-dashboard',me.registrationDashboardVm,
                                            function() {
                                                me.registrationDashboardVm.initialize(
                                                    function() {
                                                        me.application.loadComponent('modal-confirm', function () {
                                                            successFunction();
                                                        });
                                                    }
                                                );
                                            });
                                    });
                                });
                        });
                });
        }

        afterInit = () => {
            var me = this;
            me.application.bindSection('tabs');
            me.application.bindSection('registrations-form');
            me.application.bindNode('registration-finance-form');
            me.application.bindNode('assignments-form');
            me.application.bindNode('reports-form');
            me.application.showDefaultSection();
            me.getCounts();

        };

        getCounts = () => {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();

             me.peanut.executeService('registration.GetRegistrationCount',request, me.handleGetCountsResponse);
        };

        private handleGetCountsResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.counts.registrations(serviceResponse.Value.registrations);
                me.counts.attenders(serviceResponse.Value.attenders);
            }
        };


        showRegistrationForm = () => {
            var me=this;
            if (me.registrationChanged) {
                me.registrationChanged = false;
                me.registrationDashboardVm.refresh();
            }
            me.currentForm('registrations');
        };

        showFinanceForm      = () => {
            var me=this;

            if (me.registrationFinanceVm) {
                if (me.registrationFinanceVm.registrationId() != me.selectedRegistrationId()) {
                    me.registrationFinanceVm.getAccount(me.selectedRegistrationId());
                }
                else {
                    me.currentForm('finance');
                }
            }
            else {
                me.application.bindComponent('registration-finance',
                    function() {
                        me.registrationFinanceVm = new registrationFinanceComponent(me.application,me);
                        return me.registrationFinanceVm;
                    },
                    function() {
                        // initialize
                        me.registrationFinanceVm.getAccount(me.selectedRegistrationId());
                    }
                );
            }

        };

        showHousingAssignmentsForm = () => {
            var me = this;
            if (me.housingAssignmentsVM) {
                if (me.housingAssignmentsVM.registrationId() != me.selectedRegistrationId()) {
                    me.housingAssignmentsVM.getAssignments(me.selectedRegistrationId());
                }
                else {
                    me.currentForm('housing-assignments');
                }
            }
            else {
                me.application.bindComponent('housing-assignment',
                    function () {
                        me.housingAssignmentsVM = new housingAssignmentComponent(me.application, me);
                        me.housingAssignmentsVM.hideCloseButton();
                        return me.housingAssignmentsVM;
                    },
                    function () {
                        me.housingAssignmentsVM.initialize(function() {
                            me.housingAssignmentsVM.getAssignments(me.selectedRegistrationId());
                        });
                    }
                );
            }
        };

        showReportsForm      = () => {
            var me=this;
            if (me.adminReportsVm) {
                me.currentForm('reports');
            }
            else {
                me.application.bindComponent('admin-reports',
                    function () {
                        me.adminReportsVm = new adminReportsComponent(me.application, me);
                        return me.adminReportsVm;
                    },
                    function () {
                        // initialize
                        me.adminReportsVm.initialize(function() {
                            me.currentForm('reports');
                        });
                    }
                );
            }
        };

        handleEvent = (eventName:string, data:any = null)=> {
            var me = this;
            switch(eventName) {
                case 'houingassignmentsloaded' :
                    me.currentForm('housing-assignments');
                    break;
                case 'accountloaded' :
                    me.currentForm('finance');
                    break;
                case 'registrationselected' :
                    if (data) {
                        me.registrationDashboardVm.getRegistration(data);
                    }
                    else {
                        me.selectedRegistrationId(0);
                        me.registrationLookupVm.hideResults();
                    }
                    me.registrationChanged = false;
                    break;
                case 'registrationdashboardloaded' :
                    me.selectedRegistrationId(data);
                    me.currentForm('registrations');
                    break;
                case 'dashboardclosed' :
                    me.selectedRegistrationId(0);
                    me.registrationChanged = false;
                    break;
                case 'registrationchanged' :
                    me.registrationChanged = true;
                    break;
                case 'housingassignmentsrequested' :
                    if (me.selectedRegistrationId() == data) {
                        me.currentForm('housing-assignments');
                    }
                    else {
                        me.registrationDashboardVm.getRegistration(data, 'registrationhousingloaded');
                        me.registrationChanged = false;
                    }
                    break;
                case 'registrationhousingloaded' :
                    me.registrationChanged = false;
                    me.selectedRegistrationId(data);
                    me.showHousingAssignmentsForm();
                    break;
            }
        };

        getRegistrationContext = (next:(context:Tops.IRegistrationContext)=>void)=> {
            var me = this;
            if (me.context) {
                next(me.context);
            }
            else {
                me.peanut.executeService('registration.GetSessionInfo',null,
                    function(serviceResponse: IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            me.context = <IRegistrationContext>serviceResponse.Value;
                            next(me.context);
                        }
                        else {
                            next(null);
                        }
                    }
                )
            }
        };

    }
}

Tops.RegistrationAdminViewModel.instance = new Tops.RegistrationAdminViewModel();
(<any>window).ViewModel = Tops.RegistrationAdminViewModel.instance;