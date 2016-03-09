///<reference path="USDollars.ts"/>
///<reference path="selectListObservable.ts"/>
/**
 * Created by Terry on 2/29/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class incomeReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        payments : IIncomeReportItem[];
        paymentList = ko.observableArray<IIncomeReportItem>();
        checkTotal = ko.observable('');
        cashTotal = ko.observable('');
        incomeTotal = ko.observable('');
        checkCount = ko.observable(0);
        cashCount = ko.observable(0);
        incomeCount = ko.observable(0);

        sort : selectListObservable;
        filter: selectListObservable;

        selectedPayment = ko.observable(
            {
                payor: '',
                registrationId: 0,
                registrationName: '',
                type: '',
                checkNumber: '',
                amountFormatted: '',
                paymentType: 0
            }
        );



        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
            me.sort = new selectListObservable(me.applySort,[
                {Name: 'Payor name' , Value: 1},
                {Name: 'Amount' , Value: 2}],1);
            me.sort.subscribe();
            me.filter = new selectListObservable(me.applyFilter,[
                {Name: 'Show all' , Value: 0},
                {Name: 'Cash'  , Value: 1},
                {Name: 'Checks', Value: 2}],0);
            me.filter.subscribe();
        }

        applySort = (selection: INameValuePair) => {
            var me = this;
            me.sortAndFilter(me.filter.getValue(),selection.Value);
        };

        applyFilter = (selection: INameValuePair) => {
            var me = this;
            me.sortAndFilter(selection.Value, me.filter.getValue());
        };

        initialize = (data: any) => {
            var me = this;
            me.display(data);
        };

        display = (data:any)=> {
            var me = this;
            me.payments = data;
            me.setTotals();
            me.sortAndFilter();
        };

        private setTotals() {
            var me = this;
            var checkAmount = 0.00;
            var cashAmount = 0.00;
            var totalAmount = 0.00;
            var checkCount = 0;
            var cashCount = 0;
            var totalCount = 0;
            _.each(me.payments, function(item: IIncomeReportItem){
                if (item.paymentType == 1) {
                    cashAmount += Number(item.amount);
                    cashCount += 1;
                }
                else {
                    checkAmount += Number(item.amount);
                    checkCount += 1;
                }
            });
            me.cashTotal(USDollars.toUSD(cashAmount,'(no cash recieved)'));
            me.checkTotal(USDollars.toUSD(checkAmount,'(no checks recieved)'));
            me.incomeTotal(USDollars.toUSD(checkAmount + cashAmount,'(no income recieved)'));
            me.cashCount(cashCount);
            me.checkCount(checkCount);
            me.incomeCount(checkCount + cashCount);
        }

        sortAndFilter(filterValue = 0, sortValue = 1) {
            var me = this;
            var list =  _.sortBy(me.payments, function(item: IIncomeReportItem) {
                   return sortValue == 1 ?  item.payor.toLowerCase() : Number(item.amount);
                });

            if (filterValue > 0) {
                list = _.filter(list, function(item: IIncomeReportItem) {
                     return item.paymentType == filterValue;
                } ) ;
            }
            me.paymentList([]);
            me.paymentList(list);
        }

        select = ()  => {
            var me = this;
            if (me.paymentList().length == 0) {
                me.owner.getReportData(me.reportName,me.display);
            }
        };

        showPaymentNotes = (item: IIncomeReportItem) => {
            var me = this;
            me.selectedPayment(item);
            jQuery("#payment-notes-modal").modal('show');
        };

        lookupSelectedRegistration = () => {
            var me = this;
            jQuery("#payment-notes-modal").modal('hide');
            me.owner.handleEvent('registrationselected', me.selectedPayment().registrationId);
        };


        lookupRegistration =  (item: IIncomeReportItem) => {
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
                    else {
                        me.paymentList([]);
                    }
                    break;
            }
        };
    }
}
