///<reference path="selectListObservable.ts"/>
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
    export class creditsReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        data : ICreditsReportItem[] = [];
        dataList = ko.observableArray();
        showAttendedOnly = ko.observable(false);
        filter : selectListObservable;
        headerTitle = ko.observable('');
        // printHeader = ko.observable('');
        creditsTotal = ko.observable('');

        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
            me.filter = new selectListObservable(me.onFilterSelected);
        }

        onFilterSelected = (selection: INameValuePair) => {
            var me = this;
            me.applyFilter();
        };

        private getCreditsTotal() {
            var me = this;
            var list = me.dataList();
            var total = 0.00;
            _.each(list,function(item: ICreditsReportItem) {
                total += Number(item.amount);
            });
            var amount = USDollars.format(total,'(none)');
            me.creditsTotal(amount);
        }

        applyFilter = () => {
            var me = this;
            me.headerTitle(me.filter.getName(''));
            var selected = me.filter.getValue(0);
            var showAll = !me.showAttendedOnly();
            if (selected) {
                let filtered = _.filter(me.data,function(item: ICreditsReportItem) {
                    return item.creditTypeId == selected && (showAll || item.attended == 'Yes');
                });
                me.dataList(filtered);
            }
            else {
                if (showAll) {
                    me.dataList(me.data);
                }
                else {
                    let filtered = _.filter(me.data, function (item:ICreditsReportItem) {
                        return item.attended == 'Yes';
                    });
                    me.dataList(filtered);
                }
            }
            me.getCreditsTotal();
            return true;
        };

        lookupRegistration =  (item: ICreditsReportItem) => {
            var me = this;
            me.owner.handleEvent('registrationselected', item.registrationId);
        };

        initialize = (data: any) => {
            var me = this;
            me.filter.setOptions(data.creditTypes);
            me.filter.subscribe();
            me.display(data);

        };

        display = (data:any)=> {
            var me = this;
            me.data = <ICreditsReportItem[]> _.sortBy(data.report,'registrationName');
            me.applyFilter();
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
