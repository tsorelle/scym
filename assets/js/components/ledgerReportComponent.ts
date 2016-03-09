///<reference path="selectListObservable.ts"/>
/**
 * Created by Terry on 2/26/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class ledgerReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        reportView : selectListObservable;
        showAttendedOnly = ko.observable(true);
        showBalanceSheet = ko.observable(true);
        reportHeader = ko.observable('');
        ledger: ILedgerReportItem[] = [];
        balances: IBalanceSheetItem[] = [];
        itemlist = ko.observableArray<ILedgerReportItem>();
        balanceList = ko.observableArray<IBalanceSheetItem>();

        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
            me.reportView = new selectListObservable(me.showReportView,[
                    {Name: 'Balance Sheet' , Value: 0},
                    {Name: 'Charges'  , Value: 1},
                    {Name: 'Donations', Value: 2},
                    {Name: 'Credits'  , Value: 3},
                    {Name: 'Payments' , Value: 4}],0);
            me.reportView.subscribe();
        }

        showReportView = () => {
            var me = this;
            me.reportHeader(me.reportView.getName());
            var itemGroup = me.reportView.getValue();
            var showAll = !me.showAttendedOnly();

            if (itemGroup) {
                me.itemlist([]);
                let filtered = _.filter(me.ledger,function(item: ILedgerReportItem) {
                    return item.ItemGroup == itemGroup && (showAll || item.attendedCount > 0)
                });
                me.itemlist(filtered);
                me.showBalanceSheet(false);
            }
            else {
                me.balanceList([]);
                if (showAll) {
                    me.balanceList(me.balances);
                }
                else {
                    let filtered = _.filter(me.balances, function (item:IBalanceSheetItem) {
                        return ( item.attended > 0);
                    });
                    me.balanceList(filtered);
                }
                me.showBalanceSheet(true);
            }
            return true;
        };

        initialize = (data: any) => {
            var me = this;
            me.display(data);
        };

        display = (data: ILedgerReport)=> {
            var me = this;
            me.ledger = _.sortBy(data.ledger,'name');
            me.balances = _.sortBy(data.balanceSheet,'name');
            me.showReportView();
        };

        select = ()  => {
            var me = this;
            if (me.ledger.length == 0) {
                me.owner.getReportData(me.reportName,me.display);
            }
        };

        lookupRegistration =  (item: any) => {
            var me = this;
            me.owner.handleEvent('registrationselected', item.registrationId);
        };

        handleEvent = (eventName:string, data?:any)=>{
            var me = this;
            switch (eventName) {
                case 'refreshReport' :
                    if (data == me.reportName) {
                        me.owner.getReportData(me.reportName,me.display);
                    }
                    break;
            }
        };
    }
}
