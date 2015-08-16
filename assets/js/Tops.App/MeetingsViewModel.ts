/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />


module Tops {
    export class scymMeeting {
        public meetingId  : any = null;
        public meetingName  = '';
        public state  = '';
        public area  = '';
        public affiliationCode  = '';
        public worshipTimes = '';
        public worshipLocation = '';
        public url = '';
        public detailText = '';
        public note = '';
        public latitude : any = null;
        public longitude :any = null;
        public lastUpdate = '';
        public active : number = 1;
        public updatedBy = '';
        public quarterlyMeetingId : any = null;

        public mailFormLink = '';
        public quarterlyMeeting: IListItem = null;
        public email = '';
        public editState : number = editState.created;
    }

    export class scymQuarterlyMeeting {
        public quarterlyMeetingId : any = 0;
        public quarterlyMeetingName = '';
        public description = '';
        public lastUpdate = '';
        public active : number = 1;
        public updatedBy = '';

        public editState : number = editState.created;
    }

    interface IInitMeetingsResponse {
        meetings: scymMeeting[];
        quarterlies: IListItem[];
        canEdit: boolean;
    }

    export class meetingObservable {
        public active = ko.observable(true);
        public meetingId  = ko.observable();
        public meetingName  = ko.observable('');
        public state  = ko.observable('');
        public area  = ko.observable('');
        public affiliationCode  = ko.observable('');
        public worshipTimes = ko.observable('');
        public worshipLocation = ko.observable('');
        public url = ko.observable('');
        public detailText = ko.observable('');
        public note = ko.observable('');
        public latitude = ko.observable();
        public longitude = ko.observable();
        public lastUpdate = ko.observable('');
        public updatedBy = ko.observable('');
        public quarterlyMeetingName = ko.observable('');
        public quarterlyMeetingId = ko.observable();
        public viewState = ko.observable('view');
        public hasMailbox = ko.observable(false);
        public email = ko.observable('');
        public quarterlyMeeting : KnockoutObservable<IListItem> = ko.observable(null);
        public states = ko.observableArray([]);

        // validation
        public hasErrors = ko.observable(false);
        public meetingNameError = ko.observable('');
        public affiliationCodeError = ko.observable('');
        public emailError = ko.observable('');
        public areaError = ko.observable('');

        private clearErrors() {
            var me = this;
            me.meetingNameError('');
            me.emailError('');
            me.areaError('');
            me.affiliationCodeError('');
            me.hasErrors(false);
        }

        public clear() {
            var me = this;
            me.active(true);
            me.meetingId(null);
            me.meetingName('');
            me.affiliationCode('');
            me.area('');
            me.state('');
            me.worshipLocation('');
            me.worshipTimes('');
            me.quarterlyMeetingId(null);
            me.detailText('');
            me.note('');
            me.lastUpdate('');
            me.updatedBy('');
            me.url('');
            me.hasMailbox(false);
            me.quarterlyMeetingName('');
            me.email('');
            me.latitude(null);
            me.longitude(null);
            me.quarterlyMeeting(null);
            me.clearErrors();
        }
        
        public assign(meeting: scymMeeting) {
            var me = this;
            me.meetingId(meeting.meetingId);
            me.affiliationCode(meeting.affiliationCode);
            me.area(meeting.area);
            me.detailText(meeting.detailText);
            me.lastUpdate(meeting.lastUpdate);
            me.latitude(meeting.latitude);
            me.longitude(meeting.longitude);
            me.meetingName(meeting.meetingName);
            me.note(meeting.note);
            me.quarterlyMeetingId(meeting.quarterlyMeetingId);
            me.state(meeting.state);
            me.updatedBy(meeting.updatedBy);
            me.url(meeting.url);
            me.worshipLocation(meeting.worshipLocation);
            me.worshipTimes(meeting.worshipTimes);
            me.hasMailbox(meeting.mailFormLink ? true : false);
            me.active(meeting.active ? true : false);
            me.quarterlyMeeting(meeting.quarterlyMeeting);
            me.email('');
            me.clearErrors();
        }


        public validate = (meetings : scymMeeting[]):boolean => {
            var me = this;
            me.clearErrors();
            var valid = true;
            var value = me.meetingName();
            if (!value) {
                me.meetingNameError(": Please enter the name of the meeting.");
                valid = false;
            }

            value = me.area();
            if (!value) {
                me.areaError(": Please enter the city or area where the meeting is located.");
                valid = false;
            }

            value = me.email();
            if (value) {
                var emailOk = Peanut.ValidateEmail(value);
                if (!emailOk) {
                    me.emailError(': Please enter a valid email address.');
                    valid = false;
                }
            }

            value = me.affiliationCode();
            if (value) {
                value = value.toLowerCase();
                var meeting = _.find(meetings,function(m : scymMeeting){
                    var code = m.affiliationCode;
                    if (code) {
                        return code.toLowerCase() == value;
                    }
                },me);
                if (meeting && (meeting.meetingId != me.meetingId())) {
                    me.affiliationCodeError('Affiliation codes must be unique. Another meeting uses this one.');
                    valid = false;
                }
            }
            else {
                me.affiliationCodeError(': a four or five letter affiliation code is required.');
                valid = false;
            }

            me.hasErrors(!valid);
            return valid;
        };


        public update(meeting: scymMeeting) {
            var me = this;
            meeting.affiliationCode = me.affiliationCode();
            meeting.area = me.area();
            meeting.detailText = me.detailText();
            meeting.latitude = me.latitude();
            meeting.longitude = me.longitude();
            meeting.meetingName = me.meetingName();
            meeting.note = me.note();
            meeting.state = me.state();
            meeting.url = me.url();
            meeting.worshipLocation = me.worshipLocation();
            meeting.worshipTimes = me.worshipTimes();
            meeting.quarterlyMeeting = me.quarterlyMeeting();
            meeting.email = me.email();
            if (meeting.quarterlyMeeting) {
                meeting.quarterlyMeetingId = meeting.quarterlyMeeting.Value;
            }
            else {
                meeting.quarterlyMeetingId = null;
            }
            meeting.editState = me.meetingId() ? editState.updated : editState.created;
        }
    }


    export class MeetingsViewModel implements IMainViewModel {
        static instance: Tops.MeetingsViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;
        private selectedMeeting : scymMeeting;


        // observables
        meetings  : scymMeeting[] = [];
        meetingsColumn1 = ko.observableArray<scymMeeting>();
        meetingsColumn2 = ko.observableArray<scymMeeting>();
        quarterlies = ko.observableArray<IListItem>();
        states = ko.observableArray<string>();
        meetingForm = new meetingObservable();
        meetingsDisplayRows = ko.observableArray<scymMeeting[]>();
        userCanEdit = ko.observable(false);
        quarterlyMeetingFilter = ko.observable<IListItem>();
        statesFilter = ko.observable<string>();
        activeFilter = ko.observable<boolean>(true);
        filterOn = false;
        filterType = null;
        filterValue : any = null;
        showActiveOnly = true;

        // Constructor
        constructor() {
            var me = this;
            Tops.MeetingsViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }


        /**
         * @param applicationPath - root path of application or location of service script
         * @param successFunction - page inittializations such as ko.applyBindings() go here.
         *
         * Call this function in a script at the end of the page following the closing "body" tag.
         * e.g.
         *      ViewModel.init('/', function() {
         *          ko.applyBindings(ViewModel);
         *      });
         *
         */
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            // setup messaging and other application initializations
            // initialize date popus if used
            /*
             jQuery(function() {
             jQuery( ".datepicker" ).datepicker();
             });
             */

            me.application.initialize(applicationPath,
                function() {
                    me.application.showWaiter('Initializing. Please wait...');
                    me.getInitializations(successFunction);
                }
            );
        }

        getInitializations(doneFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();

            // todo: replace fake initialization with service call
            // fake scaffolding
            var fakeresponse = me.getFakeInitResponse();
            me.handleInitializationResponse(fakeresponse);
            me.application.hideWaiter();
            if (doneFunction) {
                doneFunction();
                jQuery('#meetings-application-container').show();
            }
            // ** end fake stuff ***


            /* // real stuff:
            me.peanut.executeService('directory.InitMeetingsApp', '', me.handleInitializationResponse)
                .always(function () {
                    me.application.hideWaiter();
                    if (doneFunction) {
                        doneFunction();
             jQuery('#meetings-application-container').show();
                    }
                });
            */
        }

        applyStatesFilter = (state: string) => {
            var me = this;
            if (me.filterOn) {
                me.filterOn = false;
                me.quarterlyMeetingFilter(null);
                me.filterOn = true;
                if (state) {
                    me.filterType = 'state';
                    me.filterValue = state;
                    me.filterByState();
                }
                else {
                    me.filterActiveOnly();
                }
            }
        };

        applyQuarterlyFilter = (quarterly : IListItem) => {
            var me = this;
            if (me.filterOn) {
                me.filterOn = false;
                me.statesFilter(null);
                me.filterOn = true;
                if (quarterly) {
                    me.filterType = 'quarterly';
                    me.filterValue = quarterly.Value;
                    me.filterByQuarterly();
                }
                else {
                    me.filterActiveOnly();
                }
            }
        };

        applyActiveFilter = (filtered: boolean) => {
            var me = this;
            me.showActiveOnly = filtered;
            if (me.filterType == 'quarterly') {
                me.filterByQuarterly();
            }
            else if (me.filterType == 'state') {
                me.filterByState();
            }
            else {
                me.filterActiveOnly();
            }

        };


        filterByQuarterly() {
            var me = this;
            me.filterType = 'quarterly';
            var meetingList = _.filter(me.meetings, function (meeting:scymMeeting) {
                var meetingActive = meeting.active ? true : false;
                return (meeting.quarterlyMeetingId == me.filterValue && (meetingActive || me.showActiveOnly === false));
            }, me);
            me.loadColumns(meetingList);
        }

        filterByState() {
            var me = this;
            me.filterType = 'state';
            var meetingList = _.filter(me.meetings, function (meeting:scymMeeting) {
                var meetingActive = meeting.active ? true : false;
                return (meeting.state == me.filterValue && (meetingActive || me.showActiveOnly === false));
            }, me);
            me.loadColumns(meetingList);
        }

        filterActiveOnly() {
            var me = this;
            me.filterType = '';
            var meetingList = _.filter(me.meetings, function (meeting:scymMeeting) {
                var meetingActive = meeting.active ? true : false;
                return (meetingActive || me.showActiveOnly === false);
            }, me);
            me.loadColumns(meetingList);
        }

        handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                var response = <IInitMeetingsResponse>serviceResponse.Value;
                me.meetings = response.meetings;
                me.quarterlies(response.quarterlies);
                me.states(['Arkansas','Missouri','Louisiana','Oklahoma','Texas','Four-states area (TX,AR,OK,LA)']);
                me.quarterlyMeetingFilter.subscribe(me.applyQuarterlyFilter);
                me.statesFilter.subscribe(me.applyStatesFilter);
                me.activeFilter.subscribe(me.applyActiveFilter);
                me.userCanEdit(response.canEdit);
                me.filterType = '';
                me.filterValue = null;
                me.filterOn = true;
                me.showActiveOnly = true;
                me.filterActiveOnly();
            }
            else {
                // me.userCanEdit(false);
            }
        };

        private loadColumns(meetingsList : scymMeeting[]) {
            var me = this;
            var count = meetingsList.length;
            var meetings = [];
            var half = Math.ceil( count / 2 ) - 1;
            for (var i=0; i < count; i++) {
                var meeting = meetingsList[i];
                meetings.push(meeting);
                if (i == half) {
                    me.meetingsColumn1(meetings);
                    meetings = [];
                }
            }
            me.meetingsColumn2(meetings);
        }

        private getQuarterlyMeetingById(quarterlyId : any, quarters : IListItem[]) : IListItem {
            var me = this;
            var result = _.find(quarters,function(quarter: IListItem) {
                return quarter.Value == quarterlyId;
            },me);
            return result;
        }

        createMeeting = () => {
            var me = this;
            me.selectedMeeting = new scymMeeting();
            me.selectedMeeting.editState = editState.created;
            me.selectedMeeting.meetingId = 0;
            me.meetingForm.assign(me.selectedMeeting);
            me.meetingForm.viewState('edit');
            me.showForm();
        };

        showMeetingForm = (meeting: scymMeeting) => {
            var me = this;
            me.selectedMeeting = meeting;
            me.meetingForm.assign(meeting);
            me.meetingForm.viewState('view');
            me.showForm();
        };

        editMeeting = () => {
            var me = this;
            me.meetingForm.viewState('edit');
        };

        cancelEdit = () => {
            var me = this;
            if (me.meetingForm.meetingId()) {
                me.meetingForm.assign(me.selectedMeeting);
                me.meetingForm.viewState('view');
            }
            else {
                me.hideForm();
            }
        };

        updateMeeting = () => {
            var me = this;
            if (me.meetingForm.validate(me.meetings)) {
                var meeting = me.selectedMeeting;
                me.meetingForm.update(meeting);
                me.hideForm();

                me.application.hideServiceMessages();
                me.application.showWaiter('Updating meeting...');

                var fakeResponse = new fakeServiceResponse(meeting);
                me.handleMeetingUpdate(fakeResponse);
                me.application.hideWaiter();

                // todo: implement service call to do update
                /*
                 me.peanut.executeService('meetings.updateMeeting',meeting, me.handleMeetingUpdate)
                 .always(function() {
                 me.application.hideWaiter();
                 });
                 */
            }

        };

        private handleMeetingUpdate = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var meeting = <scymMeeting>serviceResponse.Value;
                // todo: handle update response
            }
        };



        hideForm() {
            jQuery("#meeting-detail-modal").modal('hide');
        }

        showForm() {
            var me = this;
            // me.clearValidation();
            jQuery("#meeting-detail-modal").modal('show');
        }

        hideConfirmForm() {
            jQuery("#confirm-delete-modal").modal('hide');
        }

        showConfirmForm() {
            var me = this;
            jQuery("#confirm-delete-modal").modal('show');
        }


        // Temporary for testing
        public serviceCallTemplate() {
            // todo: delete serviceCallTemplate when not needed
            var me = this;
            var request = null; //

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');
            me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private handleServiceResponseTemplate = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {


            }
        };

        private getFakeInitResponse() {
            var me = this;
            var response = {
                canEdit: true,
                quarterlies: [
                    {
                        Text: 'Cielo Grande',
                        Value: 1,
                        Description: ''
                    },
                    {
                        Text: 'Bayou',
                        Value: 2,
                        Description: ''
                    },
                    {
                        Text: 'Arkansas-Oklahoma',
                        Value: 3,
                        Description: ''
                    }
                ],
                meetings: []
            };

            response.meetings.push(me.createFakeMeeting( 1,'Alpine Friends Worship Group','AL TX',null,'Alpine','Texas','http://www.quakerfinder.org/quaker/near/TX/Alpine/12461',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting( 2,'Friends Meeting of Austin','AU TX',1,'Austin','Texas','http://www.austinquakers.org',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting( 3,'Baton Rouge Friends Meeting','BA LA',2,'Baton Rouge','Louisiana','http://www.batonrougefriends.net',response.quarterlies,false));
            response.meetings.push(me.createFakeMeeting( 5,'Coastal Bend Friends Meeting','CC TX',2,'Corpus Christi','Texas','http://www.quakerfinder.org/quaker/near/TX/Corpus%20Christi/12266',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting( 6,'Bryan-College Station Friends Worship Group','CS TX',2,'Bryan-College Station','Texas',null,response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting( 7,'Dallas Friends Meeting','DA TX',1,'Dallas','Texas','http://www.dallasquakers.org/',response.quarterlies,false));
            response.meetings.push(me.createFakeMeeting( 8,'Fayetteville Friends Meeting','FA AR',3,'Fayetteville','Arkansas','http://fayettevillefriends.org',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting( 9,'Fort Worth Monthly Meeting','FW TX',null,'Fort Worth','Texas','http://www.scym.org/fortworth',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(10,'Galveston Friends Meeting','GA TX',2,'Galveston','Texas','http://www.quakerfinder.org/quaker/near/TX/Galveston/12269',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(11,'Hill Country Friends Monthly Meeting','HC TX',null,'Hill Country','Texas','http://www.quakerfinder.org/quaker/near/TX/Kerrville/11836',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(12,'Houston Live Oak Friends Meeting','HO TX',2,'Houston','Texas','http://www.friendshouston.org/',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(13,'Little Rock Friends Meeting','LR AR',3,'Little Rock','Arkansas','http://www.scym.org/littlerock',response.quarterlies,false));
            response.meetings.push(me.createFakeMeeting(14,'Lubbock Monthly Meeting','LU TX',null,'Lubbock','Texas','http://www.lubbockquakers.org',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(15,'Friends Meeting of New Orleans','NO LA',2,'New Orleans','Louisiana','http://fmno.quaker.org',response.quarterlies,false));
            response.meetings.push(me.createFakeMeeting(16,'Oklahoma City Friends Meeting','OKC',3,'Oklahoma City','Oklahoma','http://rsof-okc.com',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(17,'Rio Grande Valley Worship Group','RIO',1,'Rio Grande Valley','Texas','http://www.rgvquakers.org',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(18,'Friends Meeting of San Antonio','SA TX',1,'San Antonio','Texas','http://www.sanantonioquakers.org',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(19,'Stillwater Friends Meeting','ST OK',3,'Stillwater','Oklahoma','http://www.quakerfinder.org/quaker/near/OK/Stillwater/12191',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(20,'Sunrise Friends Meeting','SUN',null,'Springfield','Missouri','http://www.quakerfinder.org/quaker/near/MO/Springfield/12080',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(21,'Green Country Friends Meeting','TU OK',3,'Tulsa','Oklahoma','http://www.scym.org/greencountry',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(22,'Acadiana Friends Meeting','ACDN',3,'Lafayette','Four-states area (TX,AR,OK,LA)','http://www.batonrougefriends.net',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(23,'Tyler Worship Group','CDTY',null,'Tyler','Four-states area (TX,AR,OK,LA)',null,response.quarterlies,false));
            response.meetings.push(me.createFakeMeeting(24,'Ruston Worship Group','CDRU',null,'Ruston','Louisiana',null,response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(25,'Texarkana Worship Group','CDTA',null,'Texarkana','Four-states area (TX,AR,OK,LA)','http://www.quakerfinder.org/quaker/near/AR/Texarkana/12389',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(26,'Norman Friends Meeting','NFM',3,'Norman','Oklahoma','http://normanquakers.org/',response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(27,'Kiamichi Worship Group','KIA',3,'McAlester','Oklahoma',null,response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(28,'Longview Worship Group','LV TX',null,'Longview','Texas',null,response.quarterlies,true));
            response.meetings.push(me.createFakeMeeting(29,'Bayou Quakers Worship Group','BY TX',2,'Houston','Texas',null,response.quarterlies,true));
            return new fakeServiceResponse(response);
        }

        private createFakeMeeting(id, name,affiliationCode, quarterlyId, area, state, url, quarterlies, active) : scymMeeting {
            var me = this;
            var meeting = new scymMeeting();
            meeting.affiliationCode = affiliationCode;
            meeting.url = url;
            meeting.active = 1;
            meeting.area = area;
            meeting.detailText = 'detail text here.';
            meeting.editState = editState.unchanged;
            meeting.lastUpdate = '2015-08-03';
            meeting.longitude = null;
            meeting.latitude = null;
            meeting.meetingId = id;
            meeting.meetingName = name;
            meeting.note = 'let have some notes';
            meeting.mailFormLink = '/mailform?box=' + meeting.affiliationCode;
            meeting.quarterlyMeetingId = quarterlyId;
            meeting.state = state;
            meeting.url = url;
            meeting.quarterlyMeeting = me.getQuarterlyMeetingById(meeting.quarterlyMeetingId,quarterlies);
            meeting.active = active;
            return meeting;
        }



    }
}

Tops.MeetingsViewModel.instance = new Tops.MeetingsViewModel();
(<any>window).ViewModel = Tops.MeetingsViewModel.instance;