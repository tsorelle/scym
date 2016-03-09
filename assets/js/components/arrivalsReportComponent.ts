///<reference path="SelectListObservable.ts"/>
/**
 * Created by Terry on 2/9/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />
module Tops {


    interface IArrivalsReportSummaryItem {
        arrivalTime : any;
        arrivalText: string;
        arrivalCount : number;
    }

    export class arrivalsReportComponent implements IReportComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IReportOwner;
        private reportName;

        summary = ko.observableArray<IArrivalsReportSummaryItem>();

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
            me.summary(data);
        };

        select = ()  => {
            var me = this;
            if (me.summary().length == 0) {
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
