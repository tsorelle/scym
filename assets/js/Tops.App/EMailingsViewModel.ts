// replace all occurances of 'EMailings' with the name of your model

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {

    export class EmailCorrection {
        public status: string;
        public address: string;
    }

    export class BounceListItem {
        public personId: number;
        public name: string;
        public email : string = '';
        public remove: boolean = false;
        public correction: string = '';
        public validation : string = '';
    }

    export class EMailingsViewModel implements IMainViewModel {
        static instance: Tops.EMailingsViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        public fileLoadMessage = ko.observable('');
        public correctionsCount = ko.observable(0);
        public corrections : EmailCorrection[] = [];
        public bounceList :  KnockoutObservableArray<BounceListItem> = ko.observableArray([]);
        public bounceValidationErrors = ko.observable('');

        // Constructor
        constructor() {
            var me = this;
            Tops.EMailingsViewModel.instance = me;
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

                    me.application.hideServiceMessages();
                    me.application.showWaiter('Initializing...');

                    me.peanut.executeService('email.InitEmailListManagement',null, me.handleInitializationResponse)
                        .always(function() {
                            me.application.hideWaiter();
                        });

                    if (successFunction) {
                        successFunction();
                    }
                }
            );
        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var bounces = <BounceListItem[]>serviceResponse.Value;
                me.setBounces(bounces);
            }
        };

        private setBounces(bounces: BounceListItem[] ) {
            var me = this;
            _.each(bounces,function(bounce : BounceListItem){
                bounce.remove = false;
            },this);

            me.bounceList(bounces);
        }

        private resetForms() {
            var me = this;
            me.fileLoadMessage('');
            me.correctionsCount(0);
            me.corrections = [];
            // me.bounceList([]);
            me.bounceValidationErrors('');
        }
        public onFileSelected = () => {
            var me = this;
            me.resetForms();
            var e = <any>document.getElementById('fileselect');
            var file = e.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = me.fileLoaded;
                reader.readAsText(file, "UTF-8");
            }
        };

        public fileLoaded = (evt: any) => {
            var me = this;
            me.peanut.hideServiceMessages();
            var text = <string>evt.target.result;
            me.correctionsCount(0);
            me.corrections = [];
            if (text) {
                var lines = text.split('\n');
                _.forEach(lines, function(line: string) {
                    if (line) {
                        var parts = line.split(',');
                        if (parts.length == 2) {
                            var correction = new EmailCorrection();
                            correction.address = parts[0];
                            correction.status = parts[1];
                            me.corrections.push(correction);
                        }
                    }
                },me);

                var count = me.corrections.length;
                if (count < 1) {
                    me.fileLoadMessage("No corrections in this file. Did you chose the right one?");
                }
                else {
                    me.fileLoadMessage('Corrections found: ' + count);
                }
                me.correctionsCount(count);

            }
            else {
                alert('read failed');
            }

        };

        public uploadCorrections = () => {
            var me = this;
            me.fileLoadMessage('');
            me.correctionsCount(0);

            var request = me.corrections;
            me.application.hideServiceMessages();
            me.application.showWaiter('Uploading corrections...');

            me.peanut.executeService('email.UploadCorrections',request, me.handleCorrectionUploadResponse)
                .always(function() {
                    me.application.hideWaiter();
                });


        };

        private handleCorrectionUploadResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var bounces = <BounceListItem[]>serviceResponse.Value;
                me.setBounces(bounces);
            }
        };

        public uploadBounceCorrections = () => {
            var me = this;
            var bounces = me.bounceList();

            me.application.hideServiceMessages();
            var errorCount = 0;
            var count = bounces.length;
            for(var i = 0; i < count; i++) {
                var correctionItem = bounces[i];
                if (correctionItem.correction) {
                    if (!Peanut.ValidateEmail(correctionItem.correction)) {
                        bounces[i].validation = "Invalid Email Correction";
                        errorCount += 1;
                        continue;
                    }
                    bounces[i].validation = '';
                }
            }

            if (errorCount == 0) {
                me.bounceValidationErrors('');
            }
            else {
                me.bounceValidationErrors('Corrections include ' + errorCount + ' invalid email addresses.');
                me.bounceList([]);
                me.bounceList(bounces);
                return;
            }

            var request = _.filter(bounces, function(bounceItem : BounceListItem) {
                if (bounceItem.remove) {
                    bounceItem.correction = '';
                    return true;
                }
                var fix = bounceItem.correction ? bounceItem.correction.trim() : '';
                return (fix != '');
            },me);

            if (request.length < 1) {
                me.application.showWarning('No corrections to upload.');
                return;
            }

            me.application.showWaiter('Uploading e-mail address corrections...');
            me.peanut.executeService('email.CorrectBounces',request, me.handleBounceUpload)
                .always(function() {
                    me.application.hideWaiter();
                });
        };

        private handleBounceUpload = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.resetForms();
                var bounces = <BounceListItem[]>serviceResponse.Value;
                me.setBounces(bounces);
            }
        };

        public cancelBounceFixes = () => {
            var me = this;
            me.resetForms();
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

Tops.EMailingsViewModel.instance = new Tops.EMailingsViewModel();
(<any>window).ViewModel = Tops.EMailingsViewModel.instance;