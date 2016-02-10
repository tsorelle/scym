/**
 * Created by Terry on 2/5/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    interface IMealCountReportItem {
        mealTime : any;
        meal : string;
        vegetarian : number;
        glutenFree : number;
        both : number;
        regular: number;
        total : number;
    }

    interface IMealCountDataItem {
        mealTime : any;
        mealName : string;
        diettype: number;
        attendersOnly : any;
        count: any;
    }

    export class mealcountsReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        registeredMeals = ko.observableArray();
        attendedMeals = ko.observableArray();
        selectedCountType = ko.observable();

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
            me.selectedCountType('Requested');
        };


        private getReportCounts(counts: IMealCountDataItem[]) {
            var result : IMealCountReportItem[] = [
                { mealTime: 43, meal: 'Thursday Dinner',   regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 51, meal: 'Friday Breakfast',  regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 52, meal: 'Friday Lunch',      regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 53, meal: 'Friday Dinner',	   regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 61, meal: 'Saturday Breakfast',regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 62, meal: 'Saturday Lunch',    regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 63, meal: 'Saturday Dinner',   regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 71, meal: 'Sunday Breakfast',  regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 },
                { mealTime: 72, meal: 'Sunday Lunch',      regular: 0, vegetarian: 0, glutenFree: 0, both: 0, total: 0 }
            ];

            _.each(result, function(reportRow: IMealCountReportItem) {
                var mealTime = reportRow.mealTime;
                for (var dietType = 0; dietType < 4; dietType += 1) {
                    var countItem = _.find(counts,function(dataItem: IMealCountDataItem) {
                        return dataItem.mealTime == mealTime && dataItem.diettype == dietType;
                    });
                    if (countItem) {
                        switch (dietType) {
                            case 0 :
                                reportRow.regular = parseInt(countItem.count);
                                break;
                            case 1 :
                                reportRow.vegetarian = parseInt(countItem.count);
                                break;
                            case 2 :
                                reportRow.glutenFree = parseInt(countItem.count);
                                break;
                            case 3 :
                                reportRow.both = parseInt(countItem.count);
                                break;
                        }
                    }
                }
                reportRow.total = reportRow.regular + reportRow.both + reportRow.glutenFree + reportRow.both;
            });
            return result;
        }
        
        display = (data:any)=> {
            var me = this;
            me.registeredMeals([]);
            me.attendedMeals([]);
            var subData = _.filter(data,function(item: IMealCountDataItem){
                return item.attendersOnly == 0;
            });
            var table = me.getReportCounts(subData);
            me.registeredMeals(table);
            subData = _.filter(data,function(item: IMealCountDataItem){
                return item.attendersOnly == 1;
            });
            if (subData.length > 0) {
                table = me.getReportCounts(subData);
                me.attendedMeals(table);
            }
        };

        select = ()  => {
            var me = this;
            if (me.registeredMeals().length == 0) {
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
