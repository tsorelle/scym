///<reference path="selectListObservable.ts"/>
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
    export class subsidiesReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        data : ISubsidiesReportItem[];
        dataList = ko.observableArray();
        filter: selectListObservable;
        headerTitle = ko.observable('');
        showAttendedOnly = ko.observable(false);


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

        applyFilter = () => {
            var me = this;
            me.headerTitle(me.filter.getName(''));
            var selected = me.filter.getValue(0);
            var showAll = !me.showAttendedOnly();
            if (selected) {
                let filtered = _.filter(me.data,function(item: ISubsidiesReportItem) {
                    return item.creditTypeId == selected && (showAll || item.attended == 'Yes');
                });
                me.dataList(filtered);
            }
            else {
                if (showAll) {
                    me.dataList(me.data);
                }
                else {
                    let filtered = _.filter(me.data, function (item:ISubsidiesReportItem) {
                        return item.attended == 'Yes';
                    });
                    me.dataList(filtered);
                }
            }
            return true;
        };

        initialize = (data: any) => {
            var me = this;
            me.filter.setOptions(
                _.filter(data.creditTypes,function(item: INameValuePair) {
                    return (item.Value != 999 && item.Value != 1)
                })
            );
            me.filter.subscribe();
            me.display(data);
        };

        lookupRegistration =  (item: ISubsidiesReportItem) => {
            var me = this;
            me.owner.handleEvent('registrationselected', item.registrationId);
        };

        display = (data:any)=> {
            var me = this;
            me.data = <ISubsidiesReportItem[]> _.sortBy(data.report,'sortName');
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
