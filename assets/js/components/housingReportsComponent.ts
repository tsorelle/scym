/**
 * Created by Terry on 1/30/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />
/// <reference path='DayGroupObservable.ts' />

module Tops {
    export class housingReportsComponent  {

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        selectedReport = ko.observable('requestCounts');

        requestCounts = ko.observableArray<IDayGroup>();

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.application.loadResources('DayGroupObservable.js',function() {
                me.getReportData();
            });
        }

        showHousingRequestCounts = () => { var me = this; me.selectedReport('requestCounts');};
        showHousingAssignmentCounts = () => { var me = this; me.selectedReport('assignedCounts');};
        showHousingDetails = () => { var me = this; me.selectedReport('housingDetail');};
        showWhoWhereReport = () => { var me = this; me.selectedReport('whoLivesWhere');};

        refreshAll = () => {
            var me = this;
            me.requestCounts([]);
            me.getReportData();
        };

        private needsData(currentReport: string) {
            var me = this;
            switch (currentReport) {
                case 'requestCounts' :
                    return me.requestCounts().length == 0;
                    break;
                default:
                    return false;
            }

        }

        getReportData() {
            var me = this;
            var currentReport = me.selectedReport();
            if (me.needsData(currentReport)) {
                var request = {
                    id: 'housing.' + currentReport
                };

                me.application.showWaiter('Getting report data ...');
                me.peanut.executeService('registration.GetReportData', request,
                    function (serviceResponse:IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            switch (currentReport) {
                                case 'requestCounts' :
                                    // me.displayRequestCounts(<IHousingRequestCountItem[]>serviceResponse.Value);
                                    DayGroupObservable.assign(me.requestCounts,
                                        <IDayGroupReportItem[]>serviceResponse.Value);
                                    break;
                                default:
                                    alert("Report not implemented");
                            }
                        }
                    }
                ).always(function () {
                    me.application.hideWaiter();
                });
            }
        }

    }
}

// Tops.TkoComponentLoader.addVM('housing-reports',Tops.housingReportsComponent);
