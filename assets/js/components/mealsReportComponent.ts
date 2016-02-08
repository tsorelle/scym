///<reference path="SelectListObservable.ts"/>
/**
 * Created by Terry on 2/7/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {

    interface IAttenderMealReportItem {
        mealTime : any;
        name: string;
        attended : any;
        glutenFree: string;
        vegetarian : string;
    }

    export class mealsReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        roster: IAttenderMealReportItem[] = [];
        attenderList = ko.observableArray();
        dietFilter: SelectListObservable;
        mealFilter: SelectListObservable;
        attendersOnly = ko.observable(false);
        tableHeader = ko.observable('Diners');
        dayHeader = ko.observable('Thursday dinner');
        sessionStarted = ko.observable(false);
        test = ko.observable('');

        public constructor(application:IPeanutClient, owner: IReportOwner, name: string) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.reportName = name;
            me.dietFilter = new SelectListObservable(
                me.onDietFilterSelected,
                [
                    {Name: 'Vegetarian', Value: 'vegetarian'},
                    {Name: 'Gluten free', Value: 'glutenfree'}
                ]
            );
            me.mealFilter = new SelectListObservable(
                me.onMealFilterSelected,
                [
                    {Name: 'Thursday dinner', Value: 43},
                    {Name: 'Friday breakfast', Value: 51},
                    {Name: 'Friday lunch', Value: 52},
                    {Name: 'Friday dinner', Value: 53},
                    {Name: 'Saturday breakfast', Value: 61},
                    {Name: 'Saturday lunch', Value: 62},
                    {Name: 'Saturday dinner', Value: 63},
                    {Name: 'Sunday breakfast', Value: 71},
                    {Name: 'Sunday lunch (simple meal)', Value: 73}
                ], 43);
        }


        initialize = (data: any) => {
            var me = this;
            if (data) {
                me.attendersOnly(data.showAttendedOnly);
                me.display(data);
                me.mealFilter.subscribe();
                me.dietFilter.subscribe();
            }
        };

        display = (data:any)=> {
            var me = this;
            me.roster = data.attenders;
            me.applyFilters();
        };

        setAttenderFilter = () => {
            var me = this;
            // me.test(me.attendersOnly() ? 'Checked' : 'Unchecked');
            me.applyFilters();
            return true;
        }

        onDietFilterSelected = (selected: INameValuePair) => {
            var me = this;
            var value = (selected) ? selected.Value : '';
            if (value) {
                me.tableHeader(selected.Name + ' diners');
            }
            else {
                me.tableHeader('Diners');
            }
            me.applyFilters();
        };

        onMealFilterSelected = (selected: INameValuePair) => {
            var me = this;
            var value = (selected) ? selected.Value : '';
            me.applyFilters();
        };

        private applyFilters() {
            var me = this;
            if (me.roster.length == 0) {
                me.attenderList([]);
            }
            else {
                var mealValue =  me.mealFilter.getValue();
                var allAttenders =  !(me.sessionStarted() && (!me.attendersOnly()));
                var dietValue = me.dietFilter.getValue();
                var data = _.filter(me.roster,function(item: IAttenderMealReportItem) {
                    if (item.mealTime == mealValue && (allAttenders || item.attended == 1)) {
                        switch (dietValue) {
                            case 'glutenfree' :
                                return item.glutenFree != '';
                            case 'vegetarian' :
                                return item.vegetarian != '';
                            default:
                                return true;
                        }
                    }
                    return false;
                });

                me.attenderList(data);
            }
        }

        select = ()  => {
            var me = this;
            if (me.attenderList().length == 0) {
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
    }
}
