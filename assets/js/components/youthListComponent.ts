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
        private refreshNeeded = true;
        // private owner : IEventSubscriber;

        private youthData : IYouthInfo[] = [];
        youthList = ko.observableArray<IYouthInfo>();

        youthDetailForm = {
            youthId: null,
            attenderId: null,
            ageGroupId : null,
            attenderNotes: ko.observable(''),
            youthNotes: ko.observable(''),
            errorMessage: ko.observable(''),
            fullName: ko.observable(''),
            specialNeeds : ko.observable(''),
            dietPreference : ko.observable(''),
            generationName : ko.observable(''),
            dateOfBirth : ko.observable(''),
            age : ko.observable(),
            gradeLevel : ko.observable(''),
            registrationName : ko.observable(''),
            meeting: ko.observable(''),
            formsSubmitted: ko.observable(false),
            sponsor : ko.observable(''),
            ageGroup : ko.observable(''),
            arrivalTimeText : ko.observable('')
        };

        selectedSortOrder = ko.observable<INameValuePair>();
        selectedFilter = ko.observable<INameValuePair>();

        ageGroupList = ko.observableArray<IAgeGroup>();
        selectedAgeGroup = ko.observable<IAgeGroup>();

        printHeader : KnockoutComputed<string>;

        private currentSort = '';
        private currentFilter = '';

        sortOrderList = ko.observableArray<INameValuePair>(
            [
                {Name: 'Last name', Value: 'lastname'},
                {Name: 'First name', Value: 'firstname'},
                {Name: 'Age', Value: 'age'},
                {Name: 'Age group', Value: 'agegroup'},
                {Name: 'Arrival time', Value: 'arrival'},
                {Name: 'Meeting', Value: 'meeting'}
            ]
        );

        filterList = ko.observableArray<INameValuePair>( );

        public constructor(application:IPeanutClient) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.printHeader = ko.computed(me.computePrintHeader);
            // me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Getting youth list...');

            me.peanut.executeService('registration.GetYouthList',request, me.handleInitialList)
                .always(function() {
                    me.application.hideWaiter();
                    if (finalFunction) {
                        finalFunction();
                    }
                });
        }

        handleEvent = (eventName:string, data?:any)=> {
            var me = this;
            switch (eventName) {
                case 'agegroupschanged' :
                    me.refreshNeeded = true;
                    break;
            }

        };

        computePrintHeader = () => {
            var me = this;
            var sort = me.selectedSortOrder();
            var filter = me.selectedFilter();
            var result = '';

            if (filter == null) {
                result = 'All youth attenders';
            }
            else {
                var values = filter.Value.split(':');
                switch (values[0]) {
                    case 'day' :
                        result = result +  "Youth attending on " + filter.Name;
                        break;
                    case 'agegroup' :
                        result = result + filter.Name;
                        break;
                }
            }
            if (sort != null) {
                result = result + ' sorted by ' + sort.Name;
            }
            return result;
        };

        getList = () => {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            // me.application.showWaiter('Getting youth...');

             me.peanut.executeService('registration.GetYouthList',request, me.handleRefreshList)
             .always(function() {
                // me.application.hideWaiter();
             });
        };

        private handleInitialList = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.youthData = <IYouthInfo[]>serviceResponse.Value;
                me.currentFilter = 'all';
                me.currentSort = 'lastname';
                me.youthList(me.youthData);
                var filterList : INameValuePair[] = me.createFilterList();
                me.filterList(filterList);
                me.selectedSortOrder({Name: 'Last name', Value: 'lastname'});
                me.selectedFilter.subscribe(me.onFilterChange);
                me.selectedSortOrder.subscribe(me.onSortOrderChange);
            }
        };

        onFilterChange = (filter: INameValuePair) => {
            var me = this;
            var sorted = me.applySort(me.youthData,me.currentSort);
            if (filter == null) {
                me.youthList(sorted);
            }
            else {
                if (me.currentFilter != filter.Value) {
                    var filtered  = me.applyFilter(sorted,filter.Value);
                    me.youthList(filtered);
                }
            }
        };

        onSortOrderChange = (sortorder: INameValuePair) => {
            var me = this;
            var sortType = 'lastname';
            if (sortorder != null) {
                sortType = sortorder.Value;
            }
            me.currentSort = sortType;
            var sorted = me.applySort(me.youthList(), sortType);
            me.youthList(sorted);
        };

        private applyFilter(list: IYouthInfo[], filterType: string) {
            var me = this;
            if (filterType = 'all') {
                return list;
            }
            var parts = filterType.split(':');
            var value = parseInt(parts[1]);
            var arriveValue = (value + 1) * 10;
            filterType = parts[0];
            var filtered = _.filter(list, function(item : IYouthInfo) {
               switch(filterType) {
                   case 'day' :
                       return (item.arrivalTime <= arriveValue && item.departureTime >= arriveValue);
                   case 'agegroup' :
                       return item.ageGroupId == value;
               }
            });
            return filtered;
        }

        private clearSort() {

        }
        
        private applySort(list : IYouthInfo[], sortType: string) {
            var me = this;
            var sorted = _.sortBy(list,function(item: IYouthInfo) {
                switch(sortType) {
                    case 'firstname' :
                        return item.firstName;
                    case 'age' :
                        return  parseInt(item.age);
                    case 'arrival' :
                        return parseInt(item.arrivalTime);
                    case 'agegroup' :
                        return parseInt(item.ageGroupCutoff);
                    case 'meeting' :
                        return item.affiliationCode;
                    default :
                        return item.lastName;
                }
            });

            return sorted;
        }

        private handleRefreshList = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {


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

        saveChanges = () => {
            var me = this;
            
            var request : IUpdateYouthRequest = {
                youthId: me.youthDetailForm.youthId,
                sponsor: me.youthDetailForm.sponsor(),
                ageGroupId: me.selectedAgeGroup().Value,
                youthNotes: me.youthDetailForm.youthNotes(),
                formsSubmitted: me.youthDetailForm.formsSubmitted()
            };

            me.application.hideServiceMessages();
            me.application.showWaiter('Updating...');

            me.peanut.executeService('registration.UpdateYouthInfo',request, me.handleUpdateYouthResponse)
             .always(function() {
                me.application.hideWaiter();
             });

        };

        private handleUpdateYouthResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var ageGroup = me.selectedAgeGroup();
                _.each(me.youthData, function(item : IYouthInfo) {
                    if (item.youthId == me.youthDetailForm.youthId) {
                        item.sponsor = me.youthDetailForm.sponsor();
                        item.ageGroupId = ageGroup.Value;
                        item.ageGroup = ageGroup.Text;
                        item.ageGroupCutoff = ageGroup.cutoffAge;
                        item.youthNotes = me.youthDetailForm.youthNotes();
                        item.formsSubmitted = me.youthDetailForm.formsSubmitted();
                    }
                });

                var list = me.applySort(me.youthData,me.currentSort);
                if (me.currentFilter) {
                    list = me.applyFilter(list,me.currentFilter);
                }
                me.youthList(list);
            }
            jQuery("#youth-update-modal").modal('hide');
        };




        private assignYouthForm(youth: IYouthInfo) {
            var me = this;
            me.youthDetailForm.youthId = youth.youthId;
            me.youthDetailForm.attenderId = youth.attenderId;
            me.youthDetailForm.ageGroupId = youth.ageGroupId;
            me.youthDetailForm.errorMessage('');
            me.youthDetailForm.attenderNotes(youth.attenderNotes);
            me.youthDetailForm.youthNotes(youth.youthNotes);
            me.youthDetailForm.fullName(youth.fullName);
            me.youthDetailForm.specialNeeds(youth.specialNeeds);
            me.youthDetailForm.dietPreference(youth.dietPreference);
            me.youthDetailForm.generationName(youth.generationName);
            me.youthDetailForm.dateOfBirth(youth.dateOfBirth);
            me.youthDetailForm.age(youth.age);
            me.youthDetailForm.gradeLevel(youth.gradeLevel);
            me.youthDetailForm.registrationName(youth.registrationName);
            me.youthDetailForm.meeting(youth.meeting);
            me.youthDetailForm.formsSubmitted(youth.formsSubmitted);
            me.youthDetailForm.sponsor(youth.sponsor);
            me.youthDetailForm.ageGroup(youth.ageGroup);
            me.youthDetailForm.arrivalTimeText(youth.arrivalTimeText);
        }

        showDetailForm = (youth : IYouthInfo) => {
            var me = this;
            me.assignYouthForm(youth);
            if (me.refreshNeeded) {
                me.getAgeGroupList();
            }
            else {
                me.showUpdateForm();
            }
        };

        showCombinedNotes = (youth : IYouthInfo) => {
            // alert('notes');
            var me = this;
            me.assignYouthForm(youth);
            jQuery("#youth-notes-modal").modal('show');
        };

        getAgeGroupList() {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            // me.application.showWaiter('Message here...');

             me.peanut.executeService('registration.GetAgeGroupList',request, me.handleGetAgeGroupList)
                .always(function() {
                // me.application.hideWaiter();
             });
        }

        private handleGetAgeGroupList = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <IAgeGroup[]>serviceResponse.Value;
                me.ageGroupList(list);
                me.showUpdateForm();
            }
        };

        private showUpdateForm() {
            var me = this;
            var list = me.ageGroupList();
            var ageGroup = _.find(list,function(item : IAgeGroup) {
                return item.Value == me.youthDetailForm.ageGroupId;
            });
            me.refreshNeeded = false;
            me.selectedAgeGroup(ageGroup);
            jQuery("#youth-update-modal").modal('show');
        }

        private createFilterList() {
            var me = this;
            var result = [];
            var sorted = _.sortBy(me.youthData, function (item:IYouthInfo) {
                return item.ageGroupCutoff;
            });
            var ageGroupId = -1;
            _.each(sorted, function (item:IYouthInfo) {
                if (item.ageGroupId != ageGroupId) {
                    ageGroupId = item.ageGroupId;
                    result.push({
                        Name: item.ageGroup,
                        Value: 'agegroup:' + item.ageGroupId
                    });
                }
            });
            result.push({Name: 'Thursday', Value: 'day:4'});
            result.push({Name: 'Friday', Value: 'day:5'});
            result.push({Name: 'Saturday', Value: 'day:6'});
            result.push({Name: 'Sunday', Value: 'day:7'});

            return result;
        }
    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.youthListComponent);
