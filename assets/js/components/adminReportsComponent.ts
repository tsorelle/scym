///<reference path="registrationsReportComponent.ts"/>
///<reference path="mealCountsReportComponent.ts"/>
/**
 * Created by Terry on 1/4/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {

    export class adminReportsComponent implements IReportOwner  {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        private reports : IReportVM[] = [];

        selectedReport = ko.observable('landing');

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
        }

        private findReport(reportName: string) {
            var me = this;
            var report : IReportVM = _.find(me.reports, function(item: any) {
                return item.name == reportName;
            });
            return report;
        }

        private loadReport(reportName: string, componentName: string, factory: (reportName: string) => any) {
            var me=this;
            if (componentName == 'not-implemented') {
                me.selectedReport(reportName);
                return;
            }
            var report = me.findReport(reportName);
            if (report == null) {
                report = {
                    name: reportName,
                    vm : null
                };
                me.application.bindComponent(componentName,
                    function() {
                        report.vm = factory(reportName);
                        me.reports.push(report);
                        return report.vm;
                    },
                    function() {
                        me.getReportData(reportName,report.vm.initialize);
                        me.selectedReport(reportName);
                    }
                );
            }
            else {
                if (report.vm.select()) {
                    me.getReportData(reportName,report.vm.display);
                }
                me.selectedReport(reportName);
            }
        };

        getReportData(reportName: string, dataHandler: (data: any) => void) {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting report data...');
            me.peanut.executeService('registration.GetReportData', { id: 'admin.' + reportName },
                function(serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        dataHandler(serviceResponse.Value);
                    }
                }
            ).always(function() {
                me.application.hideWaiter();
            });
        }

        showReptRegistrationsReceived   = () => {
            var me=this;
            me.loadReport('registrationsReceived','registrations-report', function(reportName: string) {
                return new registrationsReportComponent(me.application, me, reportName);
            });
        };

        showReptMealCounts     = () => {
            var me=this;
            me.loadReport('mealCounts','mealcounts-report',function(reportName: string) {
                return new mealCountsReportComponent(me.application, me, reportName);
            });
        };
        showReptMealRoster              = () => {
            var me=this;
            me.loadReport('mealRoster','not-implemented',function() {return null;});
        };
        showReptRegisteredAttenders     = () => {
            var me=this;
            me.loadReport('registeredAttenders','not-implemented',function() {return null;});
        };
        showReptAttendersByMeeting      = () => {
            var me=this;
            me.loadReport('attendersByMeeting','not-implemented',function() {return null;});
        };
        showReptAttendersByArrival      = () => {
            var me=this;
            me.loadReport('attendersByArrival','not-implemented',function() {return null;});
        };
        showReptNotCheckedIn            = () => {
            var me=this;
            me.loadReport('notCheckedIn','not-implemented',function() {return null;});
        };
        showReptDropIns                 = () => {
            var me=this;
            me.loadReport('dropIns','not-implemented',function() {return null;});
        };
        showReptIncompleteRegistrations = () => {
            var me=this;
            me.loadReport('incompleteRegistrations','not-implemented',function() {return null;});
        };
        showReptPaymentsReceived        = () => {
            var me=this;
            me.loadReport('paymentsReceived','not-implemented',function() {return null;});
        };
        showReptMiscCounts              = () => {
            var me=this;
            me.loadReport('miscCounts','not-implemented',function() {return null;});
        };
        showReptFinancialAid            = () => {
            var me=this;
            me.loadReport('financialAid','not-implemented',function() {return null;});
        };
        showReptLedger                  = () => {
            var me=this;
            me.loadReport('ledger','not-implemented',function() {return null;});
        };

        refreshReports = () => {
            var me = this;
            var current = me.selectedReport();
            _.each(me.reports, function(report : IReportVM) {
                report.vm.handleEvent('refreshReport',current);
            });
        };

        handleEvent = (eventName:string, data:any = null )=> {
            var me = this;
        };
    }

}

// Tops.TkoComponentLoader.addVM('component-name',Tops.adminReportsComponent);
