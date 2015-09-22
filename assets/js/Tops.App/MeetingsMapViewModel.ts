/**
 * Created by Terry on 9/21/2015.
 */

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

/// <reference path='meetings.d.ts' />
/// <reference path='../typings/googlemapsv3/google.maps.d.ts' />


module Tops {
    export class MeetingsMapViewModel implements IMainViewModel {
        static instance: Tops.MeetingsMapViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;


        // Constructor
        constructor() {
            var me = this;
            Tops.MeetingsMapViewModel.instance = me;
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

            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.

                    if (successFunction) {
                        successFunction();
                    }
                }
            );
        }

        initMap = () => {
            alert('Map init.'); // todo: just a test place holder. remove
            var me = this;
            me.application.hideServiceMessages();
            me.peanut.executeService('meetings.InitMeetingsApp', '', me.handleInitializationResponse)
                .always(function () {
                    me.application.hideWaiter();
                });
        };

        handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                var response = <IInitMeetingsResponse>serviceResponse.Value;

                /****
                 * Here's where you can initialize the map with the data returned from the service.
                 ****/
            }
            else {
                // do stuff here if errors in response
            }
        };



    }
}

Tops.MeetingsMapViewModel.instance = new Tops.MeetingsMapViewModel();
(<any>window).ViewModel = Tops.MeetingsMapViewModel.instance;