// replace all occurances of 'YmRegistration' with the name of your model

/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="user.d.ts" />
/// <reference path="registration.d.ts" />
/// <reference path="formComponents.ts" />

// reference to jqueryui required if date popups are used.
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {

    export class userObservable {
        id : any = 0;
        name = ko.observable('');
        authenticated = ko.observable(false);
        authorized = ko.observable(false);
        email = ko.observable('');

        public assign(user : IUser) {
            var me = this;
            me.id = user ? user.id : 0;
            me.name(user ? user.name : '');
            me.authenticated(user ? (user.authenticated == 1) : false);
            me.authorized(user ? (user.authorized == 1) : false);
        }
    }

    export class regButtonObservable {
        private status = 'inactive';
        isComplete = ko.observable(false);
        iconClasses = ko.observable('');
        label = ko.observable('');
        title = ko.observable('');


        constructor(label: string, status: string = 'inactive') {
            var me = this;
            me.label(label);
            me.setStatus(status);
        }

        public setStatus(status : string) {
            var me = this;
            var label = me.label();
            me.status = status;
            me.isVisible(status != 'inactive');
            switch (status) {
                case 'complete' :
                    me.css('btn btn-lg btn-block btn-success');
                    me.iconClasses('glyphicon glyphicon-ok');
                    me.title(label + " (completed)");
                    break;
                case 'incomplete' :
                    me.css('btn btn-lg btn-block btn-warning');
                    me.iconClasses('glyphicon glyphicon-forward');
                    me.title(label + " (incomplete)");
                    break;
                default :
                    me.css('btn btn-lg btn-block btn-primary');
                    me.iconClasses('');
                    me.title(label);
                    break;
            }
            me.isComplete(status == 'complete');
        }



        public isVisible = ko.observable(false);
        public css = ko.observable('');
    }

    export class financeInfoObservable extends editPanel {

        changed = ko.observable(false);

        id  = ko.observable(0);

        YMDonation : KnockoutObservable<any> = ko.observable('');
        simpleMealDonation : KnockoutObservable<any> = ko.observable('');
        financialAidRequested  : KnockoutObservable<any> = ko.observable('');
        financialAidContribution : KnockoutObservable<any> = ko.observable('');
        financialAidAmount : KnockoutObservable<any> = ko.observable('');

        public aidRequestedError = ko.observable('');
        public aidContributionError = ko.observable('');
        public aidAmountError = ko.observable('');
        public ymDonationError = ko.observable('');
        public simpleMealDonationError = ko.observable('');



        constructor() {
            super();
            var me = this;
            me.setViewState('closed');
        }

        /**
         * reset fields
         */
        public clear() {
            var me=this;
            me.clearValidations();
            me.id(0);
            me.financialAidAmount('');
            me.financialAidRequested('');
            me.financialAidContribution('');
            me.YMDonation('');
            me.simpleMealDonation('');
            me.isAssigned = false;
        }

        public clearValidations() {
            var me = this;
            me.aidAmountError('');
            me.aidContributionError('');
            me.aidRequestedError('');
            me.simpleMealDonationError('');
            me.ymDonationError('');
            me.hasErrors(false);
        }

        /**
         * set fields from person DTO
         */
        public assign = (registration: IRegistrationInfo) => {
            var me=this;
            me.id(registration.registrationId);
            me.clearValidations();
            me.changed(registration.changed);
            me.financialAidAmount(registration.financialAidAmount);
            me.financialAidContribution(registration.financialAidContribution);
            me.financialAidRequested(registration.financialAidRequested);
            me.YMDonation(registration.YMDonation);
            me.simpleMealDonation(registration.simpleMealDonation);
            me.isAssigned = true;
        };

        public updateRegistration = (registration: IRegistrationInfo) => {
            var me = this;
            registration.changed = me.changed();
            registration.financialAidAmount = me.financialAidAmount();
            registration.financialAidContribution = me.financialAidContribution();
            registration.financialAidRequested = me.financialAidContribution();
            registration.YMDonation = me.YMDonation();
            registration.simpleMealDonation = me.simpleMealDonation();
        };

        validateCurrency(value: string) : any {
            var value = value.replace(/\s+/g, '');
            var value = value.replace(',', '');
            var value = value.replace('$', '');
            if (!value) {
                return '';
            }
            var parts = value.split('.');
            if (parts.length > 2) {
                return false;
            }
            if (!jQuery.isNumeric(parts[0])) {
                return false;
            }
            if (parts.length == 1) {
                return parts[0] + '.00';
            }

            if (!jQuery.isNumeric(parts[1])) {
                return false;
            }

            return parts[0] + '.' + parts[1].substring(0,2);
        };

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;

            var amount = me.validateCurrency(me.financialAidAmount());
            if (amount === false) {
                me.aidAmountError(" Invalid amount.");
                valid = false;
            }
            else {
                me.financialAidAmount(amount);
            }

            amount = me.validateCurrency(me.financialAidContribution());
            if (amount === false) {
                me.aidContributionError(" Invalid amount.");
                valid = false;
            }
            else {
                me.financialAidContribution(amount);
            }

            amount = me.validateCurrency(me.financialAidRequested());
            if (amount === false) {
                me.aidContributionError(" Invalid amount.");
                valid = false;
            }
            else {
                me.financialAidContribution(amount);
            }

            amount = me.validateCurrency(me.YMDonation());
            if (amount === false) {
                me.ymDonationError(" Invalid amount.");
                valid = false;
            }
            else {
                me.YMDonation(amount);
            }

            amount = me.validateCurrency(me.simpleMealDonation());
            if (amount === false) {
                me.simpleMealDonationError(" Invalid amount.");
                valid = false;
            }
            else {
                me.simpleMealDonation(amount);
            }

            me.hasErrors(!valid);
            return valid;
        };
    }

    export class contactInfoObservable extends editPanel {


        name = ko.observable('');
        address = ko.observable('');
        city = ko.observable('');
        phone = ko.observable('');
        email = ko.observable('');
        notes = ko.observable('');

        public nameError = ko.observable('');
        public emailError = ko.observable('');
        public codeError = ko.observable('');

        constructor() {
            super();
            var me = this;
            me.setViewState('closed');
        }

        /**
         * reset fields
         */
        public clear() {
            var me=this;
            me.clearValidations();
            me.name('');
            me.address('');
            me.city('');
            me.phone('');
            me.email('');
        }

        public clearValidations() {
            var me = this;
            me.nameError('');
            me.emailError('');
            me.codeError('');
            me.hasErrors(false);
        }

        /**
         * set fields from person DTO
         */
        public assign = (registration: IRegistrationInfo) => {
            var me=this;
            me.clearValidations();
            me.name(registration.name);
            me.address(registration.address);
            me.city(registration.city);
            me.email(registration.email);
        };

        public updateRegistration = (registration: IRegistrationInfo) => {
            var me = this;
            registration.name = me.name();
            registration.address = me.address();
            registration.city = me.city();
            registration.email = me.email();
        };

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;

            me.hasErrors(!valid);
            return valid;
        };
    }

    export class registrationObservable {
        id = ko.observable(0);
        changed = ko.observable(false);
        registrationCode = ko.observable('');
        contactInfoForm = new contactInfoObservable();
        financeInfoForm = new financeInfoObservable();

        public clear() {
            var me = this;
            me.registrationCode('');

        }

        public assign(registration:IRegistrationInfo) {
            var me = this;
            me.id(registration.registrationId);
            me.registrationCode(registration.registrationCode);
            me.changed(registration.changed);

            me.contactInfoForm.assign(registration);
            me.financeInfoForm.assign(registration);
        }

        public updateRegistration = (registration: IRegistrationInfo) => {
            var me = this;
            registration.registrationId = me.id();
            registration.registrationCode = me.registrationCode();
            registration.changed = me.changed();
            me.contactInfoForm.updateRegistration(registration);
            me.financeInfoForm.updateRegistration(registration);
        };
    }

    export class attenderObservable extends editPanel {
        public id = ko.observable(0);
        changed = ko.observable(false);
        firstName =  ko.observable('');
        lastName =  ko.observable('');
        middleName =  ko.observable('');
        dateOfBirth =  ko.observable('');
        affiliationCode =  ko.observable('');
        otherAffiliation =  ko.observable('');
        firstTimer =  ko.observable(false);
        teacher =  ko.observable(false);
        financialAidRequested =  ko.observable(false);
        guest =  ko.observable(false);
        notes =  ko.observable('');
        linens =  ko.observable(false);
        arrivalTime  =  ko.observable('');
        departureTime  =  ko.observable('');
        housingTypeId =  ko.observable('');
        vegetarian =  ko.observable(false);
        attended =  ko.observable(false);
        singleOccupant =  ko.observable(false);
        glutenFree =  ko.observable(false);

        // todo: implement lookups for attenders
        /*
        specialNeedsTypeId =  ko.observable(''); // lookup: special needs
        generationId =  ko.observable(''); // lookup: generations
        gradeLevel =  ko.observable(''); // 'PS','K', 1 .. 13
        ageGroupId =  ko.observable(''); // lookup agegroups
        creditTypeId =  ko.observable(''); // formerly: feeCredit, lookup: creditTypes
        */

        // validation
        firstNameError = ko.observable('');  	// required
        lastNameError = ko.observable('');  		// required
        arrivalTimeError = ko.observable(''); 	// required
        departureTimeError = ko.observable(''); 	// required - on or after arrival
        dateOfBirthError = ko.observable('');  	// required if generation code is young friend


        constructor() {
            super();
            var me = this;
            me.setViewState('closed');
        }

        /**
         * reset fields
         */
        public clear() {
            var me=this;
            me.clearValidations();
            me.id(0);
            me.changed(false);
            me.firstName('');
            me.lastName('');
            me.middleName('');
            me.dateOfBirth('');
            me.affiliationCode('');
            me.otherAffiliation('');
            me.firstTimer(false);
            me.teacher(false);
            me.financialAidRequested(false);
            me.guest(false);
            me.notes('');
            me.linens(false);
            me.arrivalTime('');
            me.departureTime('');
            me.housingTypeId('');
            me.vegetarian(false);
            me.attended(false);
            me.singleOccupant(false);
            me.glutenFree(false);

            me.setSpecialNeedsType();
            me.setGeneration();
            me.setGradeLevel();
            me.setAgeGroup();
            me.setCreditType();
            me.setHousingType();
        }

        public clearValidations() {
            var me = this;
            me.firstNameError('');
            me.lastNameError('');
            me.arrivalTimeError('');
            me.departureTimeError('');
            me.dateOfBirthError('');
            me.hasErrors(false);
        }

        /**
         * set fields from DTO
         */
        public assign = (attender: IAttender) => {
            var me=this;
            me.clearValidations();
            me.id(attender.attenderId);
            me.changed(attender.changed);
            me.firstName(attender.firstName);
            me.lastName(attender.lastName);
            me.middleName(attender.middleName);
            me.dateOfBirth(attender.dateOfBirth);
            me.affiliationCode(attender.affiliationCode);
            me.otherAffiliation(attender.otherAffiliation);
            me.arrivalTime(attender.arrivalTime);
            me.departureTime(attender.departureTime);
            me.notes(attender.notes);

            //boolean
            me.firstTimer(attender.firstTimer===1);
            me.teacher(attender.teacher===1);
            me.financialAidRequested(attender.financialAidRequested===1);
            me.guest(attender.guest===1);
            me.linens(attender.linens===1);
            me.vegetarian(attender.vegetarian===1);
            me.attended(attender.attended===1);
            me.singleOccupant(attender.singleOccupant===1);
            me.glutenFree(attender.glutenFree===1);

            me.setSpecialNeedsType(attender.specialNeedsTypeId);
            me.setGeneration( attender.generationId);
            me.setGradeLevel( attender.gradeLevel);
            me.setAgeGroup( attender.ageGroupId);
            me.setCreditType( attender.creditTypeId);
            me.setHousingType( attender.housingTypeId);
        };

        public updateAttender = (attender: IAttender) => {
            var me = this;
            attender.attenderId =            me.id();
            attender.changed =               me.changed();
            attender.firstName =             me.firstName();
            attender.lastName =              me.lastName();
            attender.middleName =            me.middleName();
            attender.dateOfBirth =           me.dateOfBirth();
            attender.affiliationCode =       me.affiliationCode();
            attender.otherAffiliation =      me.otherAffiliation();
            attender.arrivalTime =           me.arrivalTime();
            attender.departureTime =         me.departureTime();
            attender.notes =                 me.notes();

            // boolean
            attender.firstTimer =            me.firstTimer() ? 1 : 0;
            attender.teacher =               me.teacher() ? 1 : 0;
            attender.financialAidRequested = me.financialAidRequested() ? 1 : 0;
            attender.guest =                 me.guest() ? 1 : 0;
            attender.linens =                me.linens() ? 1 : 0;
            attender.vegetarian =            me.vegetarian() ? 1 : 0;
            attender.attended =              me.attended() ? 1 : 0;
            attender.singleOccupant =        me.singleOccupant() ? 1 : 0;
            attender.glutenFree =            me.glutenFree() ? 1 : 0;

            attender.specialNeedsTypeId = me.getSpecialNeedsTypeId();
            attender.generationId = me.getGenerationId();
            attender.gradeLevel = me.getGradeLevel();
            attender.ageGroupId = me.getAgeGroupId();
            attender.creditTypeId = me.getCreditTypeId();
            attender.housingTypeId = me.getHousingTypeId();

        };

        private setSpecialNeedsType(id: any = null) {
            var me = this;
            // todo: implement setSpecialNeedsType
        }

        private setGeneration(id: any = null) {
            var me = this;
            // todo: implement setGeneration
        }

        private setGradeLevel(id: any = null) {
            var me = this;
            // todo: implement setGradeLevel
        }

        private setAgeGroup(id: any = null) {
            var me = this;
            // todo: implement setAgeGroup
        }

        private setCreditType(id: any = null) {
            var me = this;
            // todo: implement setCreditType
        }

        private setHousingType(id: any = null) {
            var me = this;
            // todo: implement setHousingType
        }


        private getSpecialNeedsTypeId() : any {
            // todo: implement getSpecialNeedsTypeId
            return null;
        }

        private getGradeLevel() : any {
            // todo: implement getGradeLevel
            return null;
        }

        private getAgeGroupId() : any {
            // todo: implement getAgeGroupId
            return null;
        }
        private getCreditTypeId()  : any {
            // todo: implement getCreditTypeId
            return null;
        }

        private getHousingTypeId() : any {
            // todo: implement getHousingTypeId
            return null;
        }
        private getGenerationId() : any {
            // todo implement getGenerationId
            return null;
        }

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;

            // todo: build attender form
            return true;

            if (!me.firstName()) {
                me.firstNameError('First name is required.');
                valid = false;
            }

            if (!me.lastName()) {
                me.lastNameError('First name is required.');
                valid = false;
            }

            if (!me.arrivalTime()) {
                me.arrivalTimeError('Arrival time is required. Approximate is ok.');
                valid = false;
            }

            if (!me.departureTime()) {
                me.departureTimeError('Departure time is required. Approximate is ok.');
                valid = false;
            }

            if (me.arrivalTime() && me.departureTime() && me.arrivalTime() > me.departureTime()) {
                me.arrivalTimeError('Arrival time must precede departure.  Right?');
                valid = false;
            }

            if (me.getGenerationId() == 2 && !me.dateOfBirth()) {  // todo: get generation id for kids
                me.dateOfBirthError('Date of birth is required for children. Approximate is ok.');
                valid = false;
            }

            me.hasErrors(!valid);
            return valid;
        };

    }

    class AnnualSessionInfo {
        year : any;
        startDate : any;
        endDate : any;
    }

    class startupFormObservable {
        securityAnswer = ko.observable('');
        validationError = ko.observable('');

        public validate = () => {
            var me = this;
            var answer = me.securityAnswer().replace(/\s/g, "").toLowerCase();
            me.securityAnswer('');
            if (!answer) {
                me.validationError(': Please answer the security question.');
                return false;
            }

            if (answer != 'georgefox') {
                me.validationError(': Your answer to the security question is incorrect.');
                return false;
            }

            return true;

        }
    }

    class lookupFormObservable {
        lookupCode = ko.observable('');
        validationError = ko.observable('');

        public getLookupCode = () => {
            var me = this;
            var code = me.lookupCode().trim();
            me.validationError(code ? '' : 'Please enter something.');
            return code;
        };

        public clear() {
            this.lookupCode('');
            this.validationError('');
        }
    }

    export class YmRegistrationViewModel implements IMainViewModel {
        static instance: Tops.YmRegistrationViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        debugging = ko.observable(false);

        changesToSave = ko.observable(false);
        currentForm = ko.observable('');
        sessionInfo = new AnnualSessionInfo();
        startupForm = new startupFormObservable();
        lookupForm = new lookupFormObservable();
        attenderForm = new attenderObservable();
        registrationForm = new registrationObservable();
        attenderList : KnockoutObservableArray<IAttender> = ko.observableArray([]);
        datesText = ko.observable('');
        locationText = ko.observable('');
        registrationId : KnockoutObservable<any> =  ko.observable();
        lookupCode = ko.observable('');

        registerButton = new regButtonObservable("Summary",'ready');
        contactButton =  new regButtonObservable("Contact");
        attendersButton = new regButtonObservable("Attenders");
        accountButton = new regButtonObservable("Account");
        user = new userObservable();

        formTitle = ko.observable('Registration Summary');

        // Constructor
        constructor() {
            var me = this;
            Tops.YmRegistrationViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
            // me.changesToSave =  ko.computed(me.checkForUnsavedChanges);
        }

        private getUpdatedAttenders = () => {
            var me = this;
            var attenders = me.attenderList();
            return _.filter(attenders, function(a : IAttender) {
                return a.changed;
            },me);
        };

        saveContactInfo = () => {
            var me = this;
            if (me.registrationForm.contactInfoForm.validate()) {
                me.changesToSave();
                me.registrationForm.contactInfoForm.view();
            }
            else {
                window.location.assign('#contact-button');
            }
        };

        private checkForUnsavedChanges = ()  => {
            var me = this;
            var updatedAttenders = me.getUpdatedAttenders();
            var updateCount = updatedAttenders.length;
            if (me.registrationForm.id()) {
                return (updateCount > 0 || me.registrationForm.changed());
            }
            else {
                var name = me.registrationForm.contactInfoForm.name();
                var lookup = me.registrationForm.registrationCode();
                return (name.trim() && lookup.trim() && updateCount > 0);
            }
        };

        private static getCurrentYmYear() {
            var currentTime = new Date();
            var month = currentTime.getMonth();
            var year = currentTime.getFullYear();
            if (month > 5) {
                year += 1;
            }
            return year.toString();
        }
        saveChanges = () => {
            var me = this;
            if (!me.checkReadyToSave()) {
                return;
            }

            if (me.checkRegistrationIsComplete()) {
                me.uploadChanges();
            }
            me.showSaveWarning();
        };

        private showSaveWarning() {
            // todo: implement save modal
        }

        private uploadChanges() {
            var me = this;
            var request = me.getRegistrationChanges();
            if (!request) {
                // todo: feedback for cant save - set confirmed
                return;
            }

            me.application.hideServiceMessages();
            me.application.showWaiter('Saving changes...');

            // todo: implement saveChanges service
            // fakes
            var response = new fakeServiceResponse(null);
            me.handleSaveChangesResponse(response);
            me.application.hideWaiter();
            //** end fakes

            /*
             me.peanut.executeService('registration.saveChanges',request, me.handleSaveChangesResponse)
             .always(function() {
             me.application.hideWaiter();
             });
             */


            me.changesToSave(false);

        }
        private handleSaveChangesResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                // todo: handle save changes
            }
        };


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
            window.onbeforeunload = function() {
                if (me.changesToSave()) {
                    return "**** WARNING: If you reload or leave this page your changes will be lost. ****";
                }
            };


            jQuery('[data-toggle="tooltip"]').tooltip();
            jQuery('[data-toggle="popover"]').popover();
            me.application.loadCSS('scym-registration.css',
                function() {
                    me.application.initialize(applicationPath,
                        function() {
                            me.getInitialInfo(successFunction);
                        }
                    );
                });
        }

        public getInitialInfo(successFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Loading...');

            //testing stub...
            var loggedIn =
                false;
             // true;

            var fakeSessionInfo: IAnnualSessionInfo = {
                year : '2015',
                startDate: '2015-04-02',
                endDate : '2015-05-05',
                location: 'Greene Family Camp, Bruceville, Texas',
                datesText: 'April 2nd to 5th, 2015'
            };

            var fakeResponse = loggedIn ?
                new fakeServiceResponse(
                    {
                        sessionInfo: fakeSessionInfo,
                        registrationId: 0,
                        user: {
                            id : 1,
                            name: 'Terry SoRelle',
                            authenticated : true,
                            authorized: 1,
                            email: 'terry@mail.com'
                        }
                    }
                )
               :
                new fakeServiceResponse(
                    {
                        sessionInfo: fakeSessionInfo,
                        registrationId: 0,
                        user: {
                            id : 0,
                            name: 'Guest',
                            authenticated : false,
                            authorized: 0,
                            email: ''
                        }
                    }
                );

            me.handleInitializationResponse(fakeResponse);
            me.application.hideWaiter();
            if (successFunction) {
                successFunction();
            }
            //****** End fake

            // service call
            /*
            var request = 'registrar'; //
            me.peanut.executeService('initializeRegistrationApp',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    if (successFunction) {
                        successFunction();
                    }
                });
           */
        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IRegistrationInitResponse>serviceResponse.Value;
                me.user.assign(response.user);
                me.sessionInfo.year = response.sessionInfo.year;
                me.sessionInfo.startDate = response.sessionInfo.startDate;
                me.sessionInfo.endDate = response.sessionInfo.endDate;
                me.datesText(response.sessionInfo.datesText);
                me.locationText(response.sessionInfo.location);
                me.setWelcomeForm();
                me.registrationId(response.registrationId);
                jQuery('#application-container').show();
            }
        };

        private setWelcomeForm() {
            var me = this;
            me.currentForm('welcome');
            me.formTitle('Welcome, begin or continue your registration');
        }


        public getRegistration() {
            alert("getRegistration");
        }

        public startRegistration() {
            var me = this;
            if (me.startupForm.validate()) {
                me.newRegistration();
            }
        }

        public newRegistration() {
            var me = this;
            me.registrationForm.clear();
            me.lookupForm.clear();
            me.formTitle('Begin your registration');
            me.currentForm('registration');
        }


        public startLookup() {
            var me = this;
            if (me.startupForm.validate()) {
                me.lookupForm.clear();
                me.formTitle('Find your registration');
                me.currentForm('lookup');
            }
        }

        public findRegistration() {
            var me = this;
            var code = me.lookupForm.getLookupCode();
            if (code) {
                me.application.hideServiceMessages();
                me.application.showWaiter('Finding registration...');

                // todo: remove fake and implement service
                // ** fake **
                var result = null;
                var fakeResponse = new fakeServiceResponse(null);
                me.handleRegistrationLookupResult(fakeResponse);
                me.application.hideWaiter();
                // ** end fake **
                /*

                me.peanut.executeService('registration.findByLookupCode',code, me.handleRegistrationLookupResult)
                    .always(function() {
                        me.application.hideWaiter();
                    });
                */
            }
        }

        private handleRegistrationLookupResult = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess && serviceResponse.Value) {
                // todo: implement registration edit
                alert('show ,registration form');
            }
            else {
                me.lookupForm.validationError('Sorry, cannot locate this registration.')
            }
        };

        onLookupCodeEntered() {
            var me = this;
            var code = me.lookupForm.getLookupCode();
            if (code) {
                me.registrationForm.contactInfoForm.edit();
                me.registrationForm.changed(true);
                me.registrationForm.registrationCode(code);
                if (Peanut.ValidateEmail(code)) {
                    me.registrationForm.contactInfoForm.email(code);
                }
                me.contactButton.setStatus('incomplete');
                me.editContactInfo();
            }
        }

        endContactEdit() {
            window.location.assign('#nav-column');
            var me = this;
            var ready = me.registrationForm.contactInfoForm.validate();
            if (!ready) {
                window.location.assign('#contact-button');
                return false;
            }
            if (me.registrationForm.id()) {
                me.registrationForm.contactInfoForm.view();
            }
            me.contactButton.setStatus('complete');
            return true;
        }

        addAttenders() {
            var me = this;
            var ready = me.endContactEdit();
            if (!ready) {
                window.location.assign('#nav-column');
                return;
            }
            me.attendersButton.setStatus('incomplete');
            me.newAttender();
        }

        saveFinanceInfo = () => {
            var me = this;
            if (!me.registrationForm.financeInfoForm.validate()) {
                window.location.assign('#account-button');

                return;
            }
            me.changesToSave(true);
            me.accountButton.setStatus('complete');
            me.registrationForm.financeInfoForm.view();
            me.showSummaryForm();
        };

        private newAttender() {
            var me = this;
            me.attenderForm.clear();
            me.currentForm('attender');
            me.formTitle('New Attender');
            me.attenderForm.edit();
            me.showAttenderForm();
        }

        saveAttenderAddNext() {
            var me = this;
            if (me.saveAttender()) {
                me.newAttender();
            }
        }

        saveAttenderAndDone() {
            var me = this;
            if (me.saveAttender()) {
                me.registrationForm.financeInfoForm.edit();
                me.accountButton.setStatus('incomplete');
                me.attendersButton.setStatus('complete');
                me.attenderForm.setViewState();
                me.showAccountsForm();
            }
        }

        cancelAttenderEdit() {
            var me = this;

        }

        showAccountsForm = () => {
            var me = this;
            me.currentForm('accounts');
            me.formTitle('Fees and Donations');
            window.location.assign('#nav-column');
        };


        showSummaryForm = () => {
            var me = this;
            me.formTitle("Registration");
            me.currentForm("registration");
            window.location.assign('#nav-column');
        };

        confirmAndSave() {
          var me = this;
            if (!me.registrationForm.financeInfoForm.validate()) {
                return;
            }
            me.registrationForm.financeInfoForm.view();
            me.saveChanges();
          // todo: what happens when save fails?
            me.showSummaryForm();
        };

        checkRegistrationIsComplete() {
            var me = this;
            return (me.attendersButton.isComplete() && me.contactButton.isComplete() && me.accountButton.isComplete());
        }

        checkReadyToSave() {
            var me = this;
            if (me.registrationForm.financeInfoForm.viewState() == 'edit') {
                if (!me.registrationForm.financeInfoForm.validate()) {
                    me.application.showError('Please complete this form');
                    return false;
                }
            }
            if (me.registrationForm.contactInfoForm.viewState() == 'edit') {
                if (!me.registrationForm.contactInfoForm.validate()) {
                    me.application.showError('Please complete the contact information form.');
                }
            }
            if (me.attenderForm.viewState() == 'edit') {
                if (!me.attenderForm.validate()) {
                    me.application.showError('Please complete this form.');
                }
            }

            return true;

        }


        saveAttender() {
            var me = this;
            if (!me.attenderForm.validate()) {
                return false;
            }
            me.changesToSave(true);
            return true;
        }

        showAttendersList() {
            var me = this;
            me.attenderForm.setViewState();
            me.showAttenderForm();
        }

        showAttenderForm = () => {
            var me = this;
            me.currentForm('attenders');
            me.formTitle('Attenders');
            window.location.assign('#nav-column');
        };

        showContactForm = () => {
            var me = this;
            me.formTitle('Contact Information');
            me.currentForm('contact');
            window.location.assign('#nav-column');
            // todo: finish address form markup
        };

        editContactForm = () => {
            var me = this;
            me.registrationForm.contactInfoForm.edit();
        };

        editAccountsForm = () => {
            var me = this;
            me.registrationForm.financeInfoForm.edit();
        };


        private editContactInfo(){
            var me = this;
            me.registrationForm.contactInfoForm.edit();
            me.registrationForm.changed(true);
            me.showContactForm();
        }

        getRegistrationChanges() {
            var me = this;
            var request =  {
                registration : {},
                attenders : []
            };
            request.attenders = me.getUpdatedAttenders();
            if (me.registrationForm.changed()) {
                me.registrationForm.updateRegistration(<IRegistrationInfo>request.registration);
            }
            else if (request.attenders.length == 0) {
                return null;
            }
            return request;
        }

        saveRegistration() {


        }

        cancelRegistrationEdit() {

        }

// TEMPLATES
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

// FAKES
        private getFakeTerryAndLizRegistration() {
            var me = this;
            var attenders = [];
            attenders[0] = me.getFakeAttender(1, 'Terry','SoRelle');
            attenders[1] = me.getFakeAttender(2, 'Liz','Yeats');
            return me.getFakeRegistration(1,"Terry SoRelle and Liz Yeats",attenders);
        }

        private getFakeRegistration(id: any, name: string, attenders: IAttender[]) {

        }

        private getFakeAttender(id: any, firstName: string, lastName) {
            return {
                // todo: create fake attender
            };
        }

        private getFakeInitResponse() {
            return {
                statusTypes: [
                    {
                        Text : '',
                        Value: 0,
                        Description: ''
                    },

                ], //IListItem[];
                specialNeedsTypes: [
                    {
                        Text : '',
                        Value: 0,
                        Description: ''
                    }
                ], //IListItem[];
                generationTypes: [
                    {
                        Text : '',
                        Value: 0,
                        Description: ''
                    }
                ], //IListItem[];
                creditTypes: [
                    {
                        Text : '',
                        Value: 0,
                        Description: ''
                    }
                ], //IListItem[];
                ageGroups: [
                    {
                        ageGroupId: 0,
                        groupName: '',
                        cutoffAge: 0,
                    },
                ], //IAgeGroup[];
                user: {
                    id: 1,
                    name: "Terry SoRelle",
                    authenticated : 1,
                    authorized: 1,
                    email: 'terry@test.org'
                }
            };
        }
    }
}

Tops.YmRegistrationViewModel.instance = new Tops.YmRegistrationViewModel();
(<any>window).ViewModel = Tops.YmRegistrationViewModel.instance;