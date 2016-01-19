/**
 * Created by Terry on 1/19/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
///<reference path="../Tops.Peanut/Peanut.d.ts"/>
///<reference path="TkoComponentLoader.ts"/>
/// <reference path='../typings/underscore/underscore.d.ts' />

module Tops {
    export class monthLookupComponent {

        monthList:KnockoutObservableArray<INameValuePair>;
        selectedMonth:KnockoutObservable<INameValuePair>;
        monthLabel:KnockoutObservable<string>;

        public constructor(params:any) {
            var me = this;
            me.monthList = ko.observableArray<INameValuePair>([
                {Name: 'January', Value: 1},
                {Name: 'February', Value: 2},
                {Name: 'March', Value: 3},
                {Name: 'April', Value: 4},
                {Name: 'May', Value: 5},
                {Name: 'June', Value: 6},
                {Name: 'July', Value: 7},
                {Name: 'August', Value: 8},
                {Name: 'September', Value: 9},
                {Name: 'October', Value: 10},
                {Name: 'November', Value: 11},
                {Name: 'December', Value: 12}
            ]);


            var defaultMonth = 1;
            if (params.month) {
                defaultMonth = params.month;
            }
            if (params.selected) {
                me.selectedMonth = params.selected;
            }
            else {
                me.selectedMonth = ko.observable<INameValuePair>();
            }

            me.setMonth(defaultMonth);

            me.monthLabel = ko.observable<string>();
            if (params.label) {
                me.monthLabel(params.label);
            }
            else {
                me.monthLabel('Month');
            }
        }

        setMonth(month: number) {
            var me = this;
            var monthObject = _.find(
                me.monthList(), function (item:INameValuePair) {
                    return item.Value == month;
                });
            me.selectedMonth(monthObject);
        }
    }
}

Tops.TkoComponentLoader.addVM('month-lookup',Tops.monthLookupComponent);
