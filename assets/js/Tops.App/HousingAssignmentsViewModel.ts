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

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class HousingAssignmentsViewModel implements IMainViewModel {
        static instance: Tops.HousingAssignmentsViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        currentForm = ko.observable('assignments');

        private housingTypesVm: any;
        private housingUnitsVm: any;


        // Constructor
        constructor() {
            var me = this;
            Tops.HousingAssignmentsViewModel.instance = me;
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
            // setup messaging and other application initializations
            // initialize date popus if used
            /*
             jQuery(function() {
             jQuery( ".datepicker" ).datepicker();
             });
             */


            me.application.initialize(applicationPath,
                function () {
                    me.application.loadResources(['housingAssignmentComponent.js', 'housingLookupComponent.js'],
                        function () {
                            me.application.registerAndBindComponent('housing-assignment',
                                new housingAssignmentComponent(me.application),
                                function () {
                                    me.application.registerAndBindComponent('housing-lookup',
                                        new housingLookupComponent(me.application,me.onRegistrationSelected),
                                        function () {
                                            successFunction();
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
            me.application.bindNode('assignments');
            me.application.bindNode('housing-units-form');
            me.application.bindNode('housing-types-form');
            me.application.showDefaultSection();
        };

        onRegistrationSelected = (regId: number) => {
            var me = this;
        };

        showAssignmentForm = () => {
            var me = this;
            me.currentForm('assignments');
        };


        showHousingUnitsForm = () => {
            var me = this;
            if (me.housingUnitsVm) {
                me.currentForm('units');
            }
            else {
                me.application.bindComponent('housing-units',
                    function() {
                        me.housingUnitsVm = new housingUnitsComponent(me.application);
                        return me.housingUnitsVm;
                    },
                    function() {
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
                        me.housingTypesVm = new housingTypesComponent(me.application);
                        return me.housingTypesVm;
                    },
                    function() {
                        me.currentForm('types');
                    }
                );
            }
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

(new Tops.HousingAssignmentsViewModel()).init();

/*
Tops.HousingAssignmentsViewModel.instance = new Tops.HousingAssignmentsViewModel();
(<any>window).ViewModel = Tops.HousingAssignmentsViewModel.instance;
*/