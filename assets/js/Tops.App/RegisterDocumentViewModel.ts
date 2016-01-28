/**
 * Created by Terry on 7/7/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

module Tops {

    export class RegisterDocumentRequest {
        public title: string;
        public filename: string;
        public access: string;
    }

    export class RegisterDocumentViewModel implements IMainViewModel {
        static instance:Tops.RegisterDocumentViewModel;
        private application:Tops.IPeanutClient;
        private peanut:Tops.Peanut;

        // Observables
        docTitle = ko.observable('');
        filename = ko.observable('');
        documentAccess = ko.observable('public');

        // Constructor
        constructor() {
            var me = this;
            Tops.RegisterDocumentViewModel.instance = me;
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
        init(applicationPath:string, successFunction?:() => void) {
            var me = this;
            // setup messaging and other application initializations
            me.application.initialize(applicationPath,
                function () {
                    // do view model initializations here.

                    if (successFunction) {
                        successFunction();
                    }
                }
            );
        }

        public submitDocument() {
            // alert("Submitting");
            var me = this;
            var request = new RegisterDocumentRequest();

            request.filename = me.filename();
            request.title = me.docTitle();
            request.access = me.documentAccess();

            // me.application.showMessage("Submit " + request.filename +'/'+request.title);

            me.application.showWaiter('Register document. Please wait...');
            me.peanut.executeService('RegisterDocument', request, function (serviceResponse:Tops.IServiceResponse) {
                if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                    var uri = '/node/' + serviceResponse.Value.toString();

                    window.location= <any>uri;

                    me.docTitle('');
                    me.filename('');
                }
            }).always(function () {
                me.application.hideWaiter();
            });
        }


    }

}

Tops.RegisterDocumentViewModel.instance = new Tops.RegisterDocumentViewModel();
(<any>window).ViewModel = Tops.RegisterDocumentViewModel.instance;