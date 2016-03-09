// replace all occurances of 'Downloads' with the name of your model

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    export class DownloadsViewModel implements IMainViewModel {
        static instance: Tops.DownloadsViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        public selectedAffiliation : KnockoutObservable<INameValuePair> = ko.observable(null);
        public affiliations = ko.observableArray<INameValuePair>([]);
        public contactsDirListingOnly = ko.observable(false);
        public addressesDirListingOnly= ko.observable(false);
        public residencesOnly = ko.observable(false);
        public includeKids = ko.observable(true);
        public newsletter = ko.observable(false);

        // Constructor
        constructor() {
            var me = this;
            Tops.DownloadsViewModel.instance = me;
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

            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.

                    var request = null; //

                    me.application.hideServiceMessages();

                    me.application.showWaiter('Initializing...');
                    me.peanut.executeService('directory.GetAffiliationsList',request, me.handleGetAffiliationsResponse)
                        .always(function() {
                            me.application.hideWaiter();
                        });

                    // initialize boostrap popovers (fields display)
                    jQuery('[data-toggle="popover"]').popover();

                    if (successFunction) {
                        successFunction();
                    }
                }
            );
        }

        private handleGetAffiliationsResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.affiliations(list);
            }
        };

        public submitContacts = () => {
            var me = this;
            var options = '';
            var affiliation = me.selectedAffiliation();
            if (affiliation) {
                var code = affiliation.Value;
                options = '?aff=' + code;
            }

            var url = "/scym/download/contacts" + options;
            jQuery("#contacts-form").attr("action", url).submit();

        };

        public download = () => {
            var me = this;
            // location.replace("http://local.scym.org/scym

        };

    }
}

Tops.DownloadsViewModel.instance = new Tops.DownloadsViewModel();
(<any>window).ViewModel = Tops.DownloadsViewModel.instance;