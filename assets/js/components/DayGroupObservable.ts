/**
 * Created by Terry on 2/2/2016.
 */
/**
 * Created by Terry on 10/29/2015.
 */
/// <reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="../typings/knockout/knockout.d.ts" />
/// <reference path="../typings/custom/head.load.d.ts" />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path="../Tops.App/registration.d.ts"/>
module Tops {

    export class DayGroupObservable {
        private static getDayText(dayNumber : number) {
            switch (dayNumber) {
                case 4: return 'Thursday';
                case 5: return 'Friday';
                case 6: return 'Saturday';
                case 7: return 'Sunday';
                default: return 'unknown';
            }
        }
        public static create(reportData: IDayGroupReportItem[]) {
            var result = [];
            for(var i = 4; i<7; i++) {
                let filtered = _.filter(reportData, function (item:IHousingRequestCountItem) {
                    return item.dayNumber == i;
                });
                if (filtered.length) {
                    result.push(
                        {
                            day: DayGroupObservable.getDayText(i),
                            items: ko.observable(filtered)
                        }
                    );
                }
            }

            return result;
        }

        public static assign(observable: KnockoutObservableArray<IDayGroup>, reportData : IDayGroupReportItem[] ) {
            var list = DayGroupObservable.create(reportData);
            observable([]);
            observable(list);
        }

        public static getCount(observable: KnockoutObservable<IDayGroup[]>) {
            var count = 0;
            var list = observable();
            _.each(list, function(group: IDayGroup) {
                count = count + group.items().length;
            });
            return count;
        }
    }
}

// Tops.TkoComponentLoader.instance = new Tops.TkoComponentLoader();
// (<any>window).TkoComponents = Tops.TkoComponentLoader.instance;