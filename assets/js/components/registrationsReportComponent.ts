/**
 * Created by Terry on 2/4/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class registrationsReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        listData = [];
        registrationsList = ko.observableArray();

        selectedSortOrder = ko.observable<INameValuePair>();
        selectedFilter = ko.observable<INameValuePair>();

        printHeader = ko.observable('');

        private currentSort = '';
        private currentFilter = '';

        sortOrderList = ko.observableArray<INameValuePair>(
            [
                {Name: 'Name', Value: 'name'},
                {Name: 'Received date', Value: 'received'},
            ]
        );

        filterList = ko.observableArray<INameValuePair>(
            [
                {Name: 'All', Value: 'all'},
                {Name: 'Confirmed', Value: 'confirmed-yes'},
                {Name: 'Not confirmed', Value: 'confirmed-no'},
            ]
        );


        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
        }
        
        setPrintHeader = () => {
            var me = this;
            switch(me.currentFilter) {
                case 'confirmed-yes':
                    me.printHeader('Confirmed registrations');
                    break;
                case 'confirmed-no' :
                    me.printHeader('Registrations not confirmed');
                    break;
                default:
                    me.printHeader('All registrations');
                    break;
            }
        };
        
        setSortOrder = (value: string) => {
            var me = this;
            var order = _.find(me.sortOrderList(),function(item: INameValuePair) {
                return item.Value == value;
            });
            me.selectedSortOrder(order);
            me.currentSort = value;
        };

        setFilter = (value: string) => {
            var me = this;
            var filter = _.find(me.filterList(),function(item: INameValuePair) {
                return item.Value == value;
            });
            me.selectedFilter(filter);
            me.currentFilter = value;
        };

        lookupRegistration = (item: any) => {
            var me = this;
            me.owner.handleEvent('registrationselected',item.registrationId);
        };

        initialize = (data: any) => {
            var me = this;

            me.setSortOrder('name');
            me.setFilter('all');
            me.selectedFilter.subscribe(me.onFilterChange);
            me.selectedSortOrder.subscribe(me.onSortOrderChange);
            me.display(data);
        };

        display = (data:any)=> {
            var me = this;
            data = me.applySort(data,me.currentFilter);
            me.listData = data;
            me.registrationsList(me.applyFilter(data, me.currentFilter));
        };

        select = ()  => {
            var me = this;
            if (me.registrationsList().length == 0) {
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
                    break;
            }
        };

        openRegistration = (item: any) => {
            alert('Open registration ' + item.registrationCode);
        };

        onFilterChange = (filter: INameValuePair) => {
            var me = this;
            var sorted = me.applySort(me.listData,me.currentSort);
            if (filter == null) {
                me.registrationsList(sorted);
            }
            else {
                if (me.currentFilter != filter.Value) {
                    var filtered  = me.applyFilter(sorted,filter.Value);
                    me.registrationsList(filtered);
                }
            }
        };

        onSortOrderChange = (sortorder: INameValuePair) => {
            var me = this;
            var sortType = 'name';
            if (sortorder != null) {
                sortType = sortorder.Value;
            }
            me.currentSort = sortType;
            var sorted = me.applySort(me.registrationsList(), sortType);
            me.registrationsList(sorted);
        };

        applyFilter = (list: any[], filterType: string) => {
            var me = this;
            me.currentFilter = filterType;
            me.setPrintHeader();
            if (filterType == 'all') {
                return list;
            }
            var filtered = _.filter(list, function(item : any) {
                switch(filterType) {
                    case 'confirmed-yes' :
                        return item.confirmed == 'Yes';
                    case 'confirmed-no' :
                        return item.confirmed == 'No';
                    default:
                        return true;
                }
            });
            return filtered;
        };

        applySort = (list : any[], sortType: string) : any[] => {
            var me = this;
            var sorted = _.sortBy(list,function(item: any) {
                switch(sortType) {
                    case 'received' :
                        return item.receivedDate;
                    default :
                        return item.name;
                }
            });
            me.currentSort = sortType;
            return sorted;
        };



    }
}
