/**
 * Created by Terry on 12/21/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
///<reference path="../Tops.Peanut/Peanut.ts"/>
module Tops {
    export class housingUnitsComponent {
        private application : IPeanutClient;
        private peanut : Peanut;
        public constructor(application : IPeanutClient) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
        }

        public getUnits() {
            var me = this;

        }

    }
}
