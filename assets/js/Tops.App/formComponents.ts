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

    // * Base class for observable container that handles search results.
    export class searchListObservable {

        searchValue = ko.observable('');
        selectionCount = ko.observable(0);
        hasMore = ko.observable(false);
        hasPrevious = ko.observable(false);
        selectionList = [];
        columnCount : number;
        maxInColumn : number;
        itemsPerPage: number;
        currentPage : number = 1;
        lastPage : number;
        itemList: INameValuePair[];
        foundCount = ko.observable(0);

        public constructor(columnCount : number, maxInColumn: number ) {
            var me = this;
            me.columnCount = columnCount;
            me.maxInColumn = maxInColumn;
            me.itemsPerPage = columnCount * maxInColumn;
            for (var i=1;i<=columnCount;i++) {
                me.selectionList[i] = ko.observableArray<INameValuePair>([]);
            }
        }


        /**
         * reset search observables
         */
        public reset() {
            var me = this;
            me.searchValue('');
            me.selectionCount(0);
            me.foundCount(0);
        }

        /**
         *  Assign result list
         */
        public setList(list : Tops.INameValuePair[]) {
            var me = this;
            me.itemList = list;
            var itemCount = list.length;
            if (itemCount == 1 && list[0].Value === null) {
                me.foundCount(itemCount - 1);
            }
            else {
                me.foundCount(itemCount);
            }
            me.selectionCount(itemCount);
            me.currentPage = 1;
            me.lastPage = Math.ceil(itemCount / me.itemsPerPage);
            me.hasMore(me.lastPage > 1);
            me.hasPrevious(false);
            me.parseColumns();
        }

        /**
         * handle next-page click
         */
        public nextPage = ()=> {
            var me = this;
            if (me.currentPage < me.lastPage) {
                me.currentPage = me.currentPage + 1;
            }

            me.hasMore(me.lastPage > me.currentPage);
            me.hasPrevious(me.currentPage > 1);
            me.parseColumns();
        };

        /**
         * handle previous-page click
         */
        public previousPage = ()=> {
            var me = this;
            if (me.currentPage > 1) {
                me.currentPage = me.currentPage - 1;
            }
            me.hasMore(me.lastPage > me.currentPage);
            me.hasPrevious(me.currentPage > 1);
            me.parseColumns();
        };

        /**
         * arrange list in observable columns
         */
        private parseColumns() {
            var me = this;
            var columns = [];
            var currentColumn = 0;
            var startIndex = me.itemsPerPage * (me.currentPage - 1);
            var lastIndex = startIndex + me.itemsPerPage;
            if (lastIndex >= me.itemList.length) {
                lastIndex = me.itemList.length - 1;
            }
            columns[0] = [];
            var j = 0;
            for(var i = startIndex; i <= lastIndex; i++) {
                columns[currentColumn][j] = me.itemList[i];
                if ((i+1) % me.maxInColumn == 0) {
                    ++currentColumn;
                    columns[currentColumn] = [];
                    j=0;
                }
                else {
                    j=j+1;
                }
            }
            for (var i=0; i< me.columnCount; i++) {
                me.selectionList[i+1](columns[i]);
            }
        }
    }
}