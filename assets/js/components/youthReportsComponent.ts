/**
 * Created by Terry on 1/15/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class youthReportsComponent  {

        private application:IPeanutClient;
        private peanut:Peanut;

        public constructor(application:IPeanutClient) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            if (finalFunction) {
                finalFunction();
            }
        }

    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.youthReportsComponent);
