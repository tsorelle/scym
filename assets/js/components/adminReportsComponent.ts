///<reference path="registrationsReportComponent.ts"/>
///<reference path="mealcountsReportComponent.ts"/>
///<reference path="mealsReportComponent.ts"/>
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />
///<reference path="attendersReportComponent.ts"/>
///<reference path="arrivalsReportComponent.ts"/>
///<reference path="ledgerReportComponent.ts"/>
///<reference path="incomeReportComponent.ts"/>
///<reference path="registrarsReportComponent.ts"/>
///<reference path="financialaidReportComponent.ts"/>
///<reference path="creditsReportComponent.ts"/>
///<reference path="subsidiesReportComponent.ts"/>

/**
 * Created by Terry on 1/4/2016.
 */

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
            me.application.loadResources(['selectListObservable.js'],finalFunction);
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
                return new mealcountsReportComponent(me.application, me, reportName);
            });
        };
        showReptMealRoster              = () => {
            var me=this;
            me.loadReport('mealRoster','meals-report',function(reportName: string) {
                return new mealsReportComponent(me.application, me, reportName);
            });
        };


        showReptRegisteredAttenders     = () => {
            var me=this;
            me.loadReport('registeredAttenders','attenders-report',
                function(reportName: string) {
                    return new attendersReportComponent(me.application,me,reportName);
                });
        };

        showReptAttendersByArrival      = () => {
            var me=this;
            me.loadReport('attendersByArrival','arrivals-report',
                function(reportName: string) {
                    return new arrivalsReportComponent(me.application,me,reportName);
                });
        };

        showReptLedger                  = () => {
            var me=this;
            me.loadReport('ledger','ledger-report',
                function(reportName: string) {
                    return new ledgerReportComponent(me.application,me,reportName);
                });
        };


        showReptPaymentsReceived        = () => {
            var me=this;
            me.loadReport('paymentsReceived','income-report',
                function(reportName: string) {
                    return new incomeReportComponent(me.application,me,reportName);
                });
        };


        showReptMiscCounts              = () => {
            var me=this;
            me.loadReport('miscCounts','registrars-report',
                function(reportName: string) {
                    return new registrarsReportComponent(me.application,me,reportName);
                });
        };

        showReptFinancialAid            = () => {
            var me=this;
            me.loadReport('financialAid','financialaid-report',
                function(reportName: string) {
                    return new financialAidReportComponent(me.application,me,reportName);
                });
        };

        showReptCredits            = () => {
            var me=this;
            me.loadReport('credits','credits-report',function(reportName: string){
                return new creditsReportComponent(me.application,me,reportName);
            });
        };

        showReptSubsidies            = () => {
            var me=this;
            me.loadReport('subsidies','subsidies-report',function(reportName: string){
                return new subsidiesReportComponent(me.application,me,reportName);
            });
        };

        showDownloads = () => {
            var me = this;
            me.selectedReport('downloads');
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
            // pass down to admim view model
            me.owner.handleEvent(eventName,data);
        };
    }

}

// Tops.TkoComponentLoader.addVM('component-name',Tops.adminReportsComponent);
