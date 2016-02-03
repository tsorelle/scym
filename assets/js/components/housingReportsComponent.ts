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
        assignmentCounts = ko.observableArray();
        occupants = ko.observableArray<IDayGroup>();
        requestCountsFilter = ko.observable('All');
        occupantsFilter = ko.observable('All');
        rosterFilter  = ko.observable('All');
        housingRoster = {
            daily: ko.observableArray<IDayGroup>(),
            unassigned: ko.observableArray(),
            visitors: ko.observableArray()
        };

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

        showHousingRequestCounts = () => {
            var me = this;
            me.selectedReport('requestCounts');
            me.getReportData();
        };
        showHousingAssignmentCounts = () => {
            var me = this;
            me.selectedReport('assignedCounts');
            me.getReportData();
        };
        showHousingRoster = () => {
            var me = this;
            me.selectedReport('housingRoster');
            me.getReportData();
        };
        showWhoWhereReport = () => {
            var me = this;
            me.selectedReport('occupants');
            me.getReportData();
        };

        refreshAll = () => {
            var me = this;
            me.requestCounts([]);
            me.assignmentCounts([]);
            me.housingRoster.daily([]);
            me.housingRoster.unassigned([]);
            me.housingRoster.visitors([]);
            me.occupants([]);
            me.getReportData();
        };

        private needsData(currentReport: string) {
            var me = this;
            switch (currentReport) {
                case 'requestCounts' :
                    return me.requestCounts().length == 0;
                case 'assignedCounts' :
                    return me.assignmentCounts().length == 0;
                case 'housingRoster' :
                    return me.housingRoster.daily().length + me.housingRoster.unassigned().length + me.housingRoster.visitors().length == 0;
                case 'occupants' :
                    return me.occupants().length == 0;
                default:
                    alert("Report not implemented");
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
                                    me.requestCountsFilter('All');
                                    DayGroupObservable.assign(me.requestCounts,
                                        <IDayGroupReportItem[]>serviceResponse.Value);
                                    break;
                                case 'assignedCounts' :
                                    me.assignmentCounts([]);
                                    me.assignmentCounts(serviceResponse.Value);
                                    break;
                                case 'housingRoster' :
                                    me.rosterFilter('All');
                                    me.setHousingRoster(serviceResponse.Value);
                                    break;
                                case 'occupants' :
                                    me.occupantsFilter('All');
                                    DayGroupObservable.assign(me.occupants,
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

        private setHousingRoster(data: IDayGroupReportItem[]) {
            var me = this;
            me.housingRoster.daily([]);
            me.housingRoster.unassigned([]);
            me.housingRoster.visitors([]);
            DayGroupObservable.assign(me.housingRoster.daily,data);
            var filtered = _.filter(data,function(item: any) {
                 return item.assignedHousingType == 'NOT ASSIGNED';
            });
            me.housingRoster.unassigned(filtered);

            filtered = _.filter(data,function(item: any) {
                return item.assignedHousingType == 'DAY';
            });
            me.housingRoster.visitors(filtered);
        }
    }
}

// Tops.TkoComponentLoader.addVM('housing-reports',Tops.housingReportsComponent);
