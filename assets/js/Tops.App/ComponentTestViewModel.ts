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

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class ComponentTestViewModel implements IMainViewModel {
        static instance: Tops.ComponentTestViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        confirmText = ko.observable('Confirm your choice');


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
        init(applicationPath: string, successFunction?: () => void) {
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

                    me.application.loadComponent('user', function() {
                        me.application.loadComponent('modal-confirm', function() {
                            me.application.loadComponent('clicker', successFunction);
                        });

                    });
                    /*
                    if (successFunction) {
                        successFunction();
                    }
                    */
                }
            );
        }

        showModal = () => {
            jQuery("#confirm-delete-modal").modal('show');
        };

        showSaveModal = () => {
            jQuery("#confirm-save-modal").modal('show');
        };

        save = () => {
            jQuery("#confirm-save-modal").modal('hide');
            alert('you saved');
        };

        confirm = ()=> {
            jQuery("#confirm-delete-modal").modal('hide');
            alert("you confirmed.");
        };

        cancel = ()=> {
            alert("you cancelled.");
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