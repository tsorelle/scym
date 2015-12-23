/**
 * Created by Terry on 12/22/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts"/>
module Tops {
    export class housingLookupComponent {
        private application : IPeanutClient;
        private peanut : Peanut;
        public constructor(application : IPeanutClient, handler : (regId: number) => void) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.onSelected = handler;
        }

        public getNext = () => {

        };

        public onSelected : (regId: number) => void;


    }
}

