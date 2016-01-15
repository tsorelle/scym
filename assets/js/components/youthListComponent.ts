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
    export class youthListComponent implements IEventSubscriber {

        private application:IPeanutClient;
        private peanut:Peanut;
        private refreshNeeded = false;
        // private owner : IEventSubscriber;

        public constructor(application:IPeanutClient) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            // me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            if (finalFunction) {
                finalFunction();
            }
        }

        handleEvent = (eventName:string, data?:any)=> {
            var me = this;
            switch (eventName) {
                case 'agegroupschanged' :
                    me.refreshNeeded = true;
                    break;
            }

        };

        public refresh(finalFunction? : () => void) {
            var me = this;
            if (me.refreshNeeded) {
                me.refreshNeeded = false;
                // todo: refresh
            }
            if (finalFunction) {
                finalFunction();
            }
        }


    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.youthListComponent);
