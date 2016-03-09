/**
 * Created by Terry on 10/26/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
module Tops {
    /**
     * base class for person panel and address panel
     */
    export class editPanel {
        public viewState = ko.observable('');
        public hasErrors = ko.observable(false);
        public isAssigned = false;
        public relationId : any = null;

        /**
         * set view state 'edit'
         */
        public edit(relationId: any = null){
            var me = this;
            me.viewState('edit');
            me.relationId = relationId;
        }
        /**
         * set view state 'closed'
         */
        public close() {
            var me = this;
            me.viewState('closed');
        }
        /**
         * set view state 'search'
         */
        public search() {
            var me = this;
            me.viewState('search');
        }
        /**
         * set view state 'empty'
         */
        public empty() {
            var me = this;
            me.viewState('empty');
        }
        /**
         * set view state 'view'
         */
        public view() {
            var me = this;
            if (me.isAssigned) {
                me.viewState('view');
            }
            else {
                me.viewState('empty');
            }
        }

        /**
         * Set view state, iqnore any constraints
         * @param state
         */
        public setViewState(state = 'view') {
            var me = this;
            me.viewState(state);
        }
    }
}