/**
 * Created by Terry on 1/15/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />
///<reference path="../components/youthListComponent.ts"/>
///<reference path="../components/ageGroupsComponent.ts"/>
///<reference path="../components/youthReportsComponent.ts"/>


module Tops {
    export class YouthViewModel implements IMainViewModel, IEventSubscriber {
        static instance: Tops.YouthViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        private youthListVM : any;
        private ageGroupsVM : any;
        private reportsVM   : any;

        currentForm = ko.observable('youthlist');

        // Constructor
        constructor() {
            var me = this;
            Tops.YouthViewModel.instance = me;
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
            successFunction = me.afterInit;
            me.application.initialize(applicationPath,
                function() {
                    me.application.loadResources(['youthListComponent.js'],
                        function() {
                            me.youthListVM = new youthListComponent(me.application);
                            me.application.registerComponent('youth-list',me.youthListVM,function() {
                                me.youthListVM.initialize(function(){
                                    successFunction();
                                    /** // replace if needed
                                    me.application.loadComponent('modal-confirm', function () {
                                        successFunction();
                                    });
                                     **/
                                });
                            });
                        }
                    );
                }
            );
        }

        handleEvent = (eventName:string, data?:any)=> {
            var me = this;
            me.youthListVM.handleEvent(eventName,data);
        };

        afterInit = () => {
            var me = this;
            me.application.bindSection('tabs');
            me.application.bindSection('youth-list-form');
            me.application.bindNode('age-groups-form');
            me.application.bindNode('reports-form');
            me.application.showDefaultSection();
        };


        showYouthList = () => {
            var me=this;
            me.currentForm('youthlist');
        };

        showAgeGroups  = () => {
            var me = this;
            if (me.ageGroupsVM) {
                me.currentForm('agegroups');
            }
            else {
                me.application.bindComponent('age-groups',
                    function () {
                        me.ageGroupsVM = new ageGroupsComponent(me.application, me);
                        return me.ageGroupsVM;
                    },
                    function () {
                        // initialize
                        me.ageGroupsVM.initialize(function() {
                            me.currentForm('agegroups');
                        });
                    }
                );
            }
        };

        showReportsForm = () => {
            var me=this;
            if (me.reportsVM) {
                me.currentForm('reports');
            }
            else {
                me.application.bindComponent('youth-reports',
                    function () {
                        me.reportsVM = new youthReportsComponent(me.application);
                        return me.reportsVM;
                    },
                    function () {
                        // initialize
                        me.currentForm('reports');
                    }
                );

            }
        };
    }
}

Tops.YouthViewModel.instance = new Tops.YouthViewModel();
(<any>window).ViewModel = Tops.YouthViewModel.instance;