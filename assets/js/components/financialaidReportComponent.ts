///<reference path="USDollars.ts"/>
/**
 * Created by Terry on 3/1/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class financialAidReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        data : IFinancialAidReportItem[];
        dataList = ko.observableArray();
        showAttendedOnly = ko.observable(false);
        totalAid = ko.observable('');

        totalAidCalculated : string;
        totalAttendedAidCalculated : string;

        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
        }

        initialize = (data: any) => {
            var me = this;
            me.display(data);
        };

        display = (data:any)=> {
            var me = this;
            me.data = data;
            var totalAidCalculated = 0.00;
            var totalAttendedAidCalculated = 0.00;
            _.each(data, function(item: IFinancialAidReportItem) {
                totalAidCalculated += Number(item.amount);
                if (item.attended == 'Yes') {
                    totalAttendedAidCalculated += Number(item.amount);
                }
            });

            me.totalAidCalculated = USDollars.format(totalAidCalculated,'(none)');
            me.totalAttendedAidCalculated = USDollars.format(totalAttendedAidCalculated,'(none)');
            me.filter();
        };

        filter = () => {
            var me = this;
            if (me.showAttendedOnly())  {
                var filtered = _.filter(me.data, function(item: IFinancialAidReportItem) {
                    return  (item.attended == 'Yes');
                });
                me.dataList(filtered);
                me.totalAid(me.totalAttendedAidCalculated);

            }
            else {
                me.dataList(me.data);
                me.totalAid(me.totalAidCalculated);
            }
            return true;
        };

        lookupRegistration =  (item: IFinancialAidReportItem) => {
            var me = this;
            me.owner.handleEvent('registrationselected', item.registrationId);
        };

        select = ()  => {
            var me = this;
            if (me.dataList().length == 0) {
                me.owner.getReportData(me.reportName,me.display);
            }
        };

        handleEvent = (eventName:string, data?:any)=>{
            var me = this;
            switch (eventName) {
                case 'refreshReport' :
                    if (data == me.reportName) {
                        me.owner.getReportData(me.reportName,me.display);
                    }
                    else {
                        me.dataList([]);
                    }
                    break;
            }
        };
    }
}
