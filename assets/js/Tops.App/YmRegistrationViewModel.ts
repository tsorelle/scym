/**
 * Created by Terry on 11/01/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="user.d.ts" />
/// <reference path="registration.d.ts" />
/// <reference path="formComponents.ts" />
/// <reference path="../components/textParser.ts" />
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {

    export class userObservable {
        id : any = 0;
        name = ko.observable('');
        authenticated = ko.observable(false);
        isRegistrar = ko.observable(false);
        email = ko.observable('');
        registrationId = ko.observable(0);

        public assign(user : IRegistrationUser) {
            var me = this;
            me.id = user ? user.id : 0;
            me.name(user ? user.name : '');
            me.isRegistrar(user ? (user.isRegistrar == 1) : false);
            me.authenticated(user ? (user.authenticated == 1) : false);
            me.email(user ? user.email : '');
            me.registrationId(user ? user.registrationId : 0);
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

        public setComplete() {
            var me = this;
            me.setStatus('complete');
        }

        public setIncomplete() {
            var me = this;
            me.setStatus('incomplete');
        }

        public isVisible = ko.observable(false);
        public css = ko.observable('');
    }

    export class financeInfoObservable extends editPanel {

        id  = ko.observable(0);

        aidRequested  : KnockoutObservable<any> = ko.observable('');
        ymDonation : KnockoutObservable<any> = ko.observable('');
        simpleMealDonation : KnockoutObservable<any> = ko.observable('');

        aidRequestedError = ko.observable('');
        ymDonationError = ko.observable('');
        simpleMealDonationError = ko.observable('');

        // summary
        feesList = ko.observableArray<IListItem>();
        creditsList = ko.observableArray<IListItem>();
        donationsList = ko.observableArray<IListItem>();
        feeTotal= ko.observable('');
        creditTotal= ko.observable('');
        donationTotal= ko.observable('');
        balance= ko.observable('');
        calculated = ko.observable(false);
        balanceDue : KnockoutObservable<any> = ko.observable();


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
            me.aidRequested('');
            me.ymDonation('');
            me.simpleMealDonation('');
            me.isAssigned = false;
            me.balance('Not yet calculated');
            me.balanceDue(null);
            me.calculated(false);
        }

        public clearValidations() {
            var me = this;
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
            me.aidRequested(registration.aidRequested);
            me.ymDonation(registration.ymDonation);
            me.simpleMealDonation(registration.simpleMealDonation);
            me.isAssigned = true;
        };

        public assignAccountSummary(summary: IAccountSummary) {
            var me = this;
            me.feesList(summary.fees);
            me.creditsList(summary.credits);
            me.donationsList(summary.donations);
            me.feeTotal(summary.feeTotal);
            me.creditTotal(summary.creditTotal);
            me.donationTotal(summary.donationTotal);
            me.balanceDue(summary.balance);
            if (jQuery.isNumeric(summary.balance)) {
                me.calculated(true);
                if (summary.balance == 0) {
                    me.balance('Paid in full');
                }
                else if (summary.balance > 0) {
                    me.balance(summary.balance);
                }
                else {
                    me.balance('Credit: ' + Math.abs(summary.balance));
                }
            }
            else {
                me.calculated(false);
                me.balance("Not calculated yet")
            }

            me.balance(summary.balance);
        }

        public updateRegistration = (registration: IRegistrationInfo) => {
            var me = this;
            registration.aidRequested = me.aidRequested();
            registration.ymDonation = me.ymDonation();
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


            var amount = me.validateCurrency(me.aidRequested());
            if (amount === false) {
                me.aidRequestedError(" Invalid amount.");
                valid = false;
            }
            else {
                me.aidRequested(amount);
            }

            amount = me.validateCurrency(me.ymDonation());
            if (amount === false) {
                me.ymDonationError(" Invalid amount.");
                valid = false;
            }
            else {
                me.ymDonation(amount);
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

        hasAddress: KnockoutComputed<boolean>;

        public nameError = ko.observable('');
        public emailError = ko.observable('');
        public codeError = ko.observable('');
        public contactError = ko.observable('');

        constructor() {
            super();
            var me = this;
            me.setViewState('closed');
            me.hasAddress = ko.computed(function() {
                return me.address() || me.city() ? true : false;
            });
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
            me.contactError('');
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
            var name = me.name().trim();
            me.name(name);
            var email = me.email().trim();
            me.email(email);
            var phone = me.phone().trim();
            me.phone(phone);
            var address = me.address().trim();
            me.address(address);
            var city = me.city().trim();
            me.city(city);
            var hasPhoneOrAddress = phone.length > 0 || (address.length > 0 && city.length > 0);

            if (!name) {
                me.nameError(': A name is required for this registration.');
                valid = false;
            }

            if (email) {
                if (!Peanut.ValidateEmail(email)) {
                    me.emailError(': This is not a valid email address');
                    valid = false;
                }
            }
            else if (!hasPhoneOrAddress) {
                me.contactError('You must enter some form of contact information: email, phone or address.')
                valid = false;
            }
            me.hasErrors(!valid);
            return valid;
        };
    }

    export class registrationObservable {
        id = ko.observable(0);
        registrationCode = ko.observable('');
        contactInfoForm = new contactInfoObservable();
        financeInfoForm = new financeInfoObservable();
        confirmed = ko.observable(false);
        familyMembers = ko.observableArray<IFamilyAttender>();

        public clear() {
            var me = this;
            me.registrationCode('');

        }

        public assign(registration:IRegistrationInfo) {
            var me = this;
            me.id(registration.registrationId);
            me.confirmed(registration.confirmed == 0 ? false : true);
            me.registrationCode(registration.registrationCode);
            me.contactInfoForm.assign(registration);
            me.financeInfoForm.assign(registration);
        }

        public updateRegistration = (registration: IRegistrationInfo) => {
            var me = this;
            registration.registrationId = me.id();
            registration.registrationCode = me.registrationCode();
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
        attended =  ko.observable(false);
        singleOccupant =  ko.observable(false);

        vegetarian =  ko.observable(false);
        glutenFree =  ko.observable(false);

        mealThursDinner =  ko.observable(false);
        mealFriBreakfast =  ko.observable(false);
        mealFriLunch =  ko.observable(false);
        mealFriDinner =  ko.observable(false);
        mealSatBreakfast =  ko.observable(false);
        mealSatLunch =  ko.observable(false);
        mealSatDinner =  ko.observable(false);
        mealSunBreakfast =  ko.observable(false);
        simpleMeal = ko.observable(false);

        // lookups
        specialNeedsTypes = ko.observableArray<IListItem>();
        housingTypes      = ko.observableArray<IHousingTypeListItem>();
        generationTypes   = ko.observableArray<IListItem>();
        affiliationCodes  = ko.observableArray<IListItem>();
        creditTypes       = ko.observableArray<IListItem>();
        gradeLevels       = ko.observableArray<IListItem>();
        ageGroups         = ko.observableArray<IAgeGroup>();
        timePeriods       = ko.observableArray<IListItem>();
        lookupsAssigned  = false;

        public selectedSpecialNeedsType  : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedHousingType       : KnockoutObservable<IHousingTypeListItem> = ko.observable(null);
        public selectedGenerationType    : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedAffiliationCode   : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedCreditType        : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedAgeGroup          : KnockoutObservable<IAgeGroup> = ko.observable(null);
        public selectedGradeLevel        : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedArrivalTime        : KnockoutObservable<IListItem> = ko.observable(null);
        public selectedDepartureTime     : KnockoutObservable<IListItem> = ko.observable(null);

        attenderFullName = ko.computed(function() {return ''}); // replaced in initialize function
        isChild = ko.computed(function() {return false}); // replaced in initialize function
        singleOccupancyApplies = ko.computed(function() {return false}); // replaced in initialize function

        // validation
        firstNameError = ko.observable('');  	// required
        lastNameError = ko.observable('');  		// required
        arrivalTimeError = ko.observable(''); 	// required
        departureTimeError = ko.observable(''); 	// required - on or after arrival
        dateOfBirthError = ko.observable('');  	// required if generation code is young friend
        housingPreferenceError = ko.observable('');

        constructor() {
            super();
            var me = this;
            me.setViewState('closed');
            // me.personFormHeader = ko.computed(me.computePersonFormHeader);

        }

        initialize = () => {
            var me = this;
            // delayed initialization for loaded resources
            me.attenderFullName = ko.computed(function() {
                return textParser.makeFullName(me.firstName(),me.lastName(),me.middleName());
            });
            me.isChild = ko.computed(function() {
                var generation = me.selectedGenerationType();
                return (generation) ?  generation.Value != 1 : false;
            });
            me.singleOccupancyApplies = ko.computed(function() {
                var housing = me.selectedHousingType();
                if (housing) {
                    return housing.category == 3;
                }
                return false
            });

        };

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
            me.vegetarian(false);
            me.attended(false);
            me.singleOccupant(false);
            me.glutenFree(false);

            me.mealThursDinner(false);
            me.mealFriBreakfast(false);
            me.mealFriLunch(false);
            me.mealFriDinner(false);
            me.mealSatBreakfast(false);
            me.mealSatLunch(false);
            me.mealSatDinner(false);
            me.mealSunBreakfast(false);
            me.simpleMeal(false);

            me.selectedSpecialNeedsType(null);
            me.selectedHousingType(null);
            me.setLookupValue(me.generationTypes(), me.selectedGenerationType, 1);
            me.selectedAffiliationCode(null);
            me.setLookupValue(me.creditTypes(),me.selectedCreditType, 0);
            me.selectedAgeGroup(null);
            me.selectedGradeLevel(null);
            me.setLookupValue(me.timePeriods(),me.selectedArrivalTime,42);
            me.setLookupValue(me.timePeriods(),me.selectedDepartureTime,72);
        }

        public clearValidations() {
            var me = this;
            me.firstNameError('');
            me.lastNameError('');
            me.arrivalTimeError('');
            me.departureTimeError('');
            me.dateOfBirthError('');
            me.housingPreferenceError('');
            me.hasErrors(false);
        }

        public assignLookupLists(lists : IAttenderLookups) {
            var me = this;
            me.lookupsAssigned = true;
            me.housingTypes(lists.housingTypes);
            me.affiliationCodes(lists.affiliationCodes);
            me.ageGroups(lists.ageGroups);

            var times = me.buildTimeLookup();
            me.timePeriods(times);
            me.generationTypes(
                [
                    { Text:'Adult',Value:'1',Description:'' },
                    { Text:'Youth (age 4 through 18)',Value:'2',Description:'' },
                    { Text:'Infant (through age 3)',Value:'5',Description:'' },
                    { Text:'',Value:'',Description:'' }
                ]
            );

            me.specialNeedsTypes(
                [
                    { Text:'Hearing impaired',Value:'1',Description:'' },
                    { Text:'Mobility impaired',Value:'2',Description:'' },
                    { Text:'Other, see notes',Value:'500',Description:'' }
                ]
            );
            me.gradeLevels(
                [
                    { Text:'Preschool',Value:'PS',Description:'' },
                    { Text:'Kindergarten',Value:'K',Description:'' },
                    { Text:'First',Value:'1',Description:'' },
                    { Text:'Second',Value:'2',Description:'' },
                    { Text:'Third',Value:'3',Description:'' },
                    { Text:'Fourth',Value:'4',Description:'' },
                    { Text:'Fifth',Value:'5',Description:'' },
                    { Text:'Sixth',Value:'6',Description:'' },
                    { Text:'Seventh',Value:'7',Description:'' },
                    { Text:'Eighth',Value:'8',Description:'' },
                    { Text:'Ninth',Value:'9',Description:'' },
                    { Text:'Tenth',Value:'10',Description:'' },
                    { Text:'Eleventh',Value:'11',Description:'' },
                    { Text:'Twelth',Value:'12',Description:'' }
                ]
            );
            me.creditTypes(
                [
                    { Text:'General attender',Value:'0',Description:'' },
                    { Text:'Teacher',Value:'2',Description:'' },
                    { Text:'Guest',Value:'3',Description:'' },
                    { Text:'SCYM Staff',Value:'4',Description:'' }
                ]
            );
        }
        
        private buildTimeLookup() : IListItem[] {
            var me = this;
            var result = [];
            var minCode = 42;
            var maxCode = 72;
            for(var day=4; day < 8; day += 1) {
                for(var time = 1; time < 4; time += 1) {
                    var code = (day * 10) + time;
                    result.push(
                        {
                            Text: me.timeCodeToStr(code),
                            Value: code,
                            Description: ''
                        });
                }    
            }
            return result;
        }
        
        private timeCodeToStr(timeCode) {
            var time = 'error';
            var day = 'error';
            switch (Math.floor(timeCode / 10)) {
                case 4 :
                    day = 'Thursday';
                    break;
                case 5 :
                    day = 'Friday';
                    break;
                case 6 :
                    day = 'Saturday';
                    break;
                case 7 :
                    day = 'Sunday';
                    break;
            }

            switch (timeCode % 10) {
                case 1 :
                    time = 'morning';
                    break;
                case 2 :
                    time = 'noon';
                    break;
                case 3 :
                    time = 'evening';
                    break;
            }

            return day+' ' + time;
        }

        public setDefaults = (attender: IAttender = null) => {
            var me = this;
            if (attender) {
                me.setAttenderMeals(attender);
                me.setLookupValue(me.timePeriods(), me.selectedArrivalTime, attender.arrivalTime);
                me.setLookupValue(me.timePeriods(), me.selectedDepartureTime, attender.departureTime);
                me.setLookupValue(me.housingTypes(), me.selectedHousingType, attender.housingTypeId);
                me.setLookupValue(me.affiliationCodes(), me.selectedAffiliationCode, attender.affiliationCode);
            }
            else {
                me.mealThursDinner(true);
                me.mealFriBreakfast(true);
                me.mealFriLunch(true);
                me.mealFriDinner(true);
                me.mealSatBreakfast(true);
                me.mealSatLunch(true);
                me.mealSatDinner(true);
                me.mealSunBreakfast(true);
                me.simpleMeal(true);
            }
        };

        private setAttenderMeals(attender: IAttender) {
            var me = this;
            attender.meals.forEach(function(mealtime: number) {
                switch(mealtime) {
                    case 43: me.mealThursDinner(true);break;
                    case 51: me.mealFriBreakfast(true);break;
                    case 52: me.mealFriLunch(true);break;
                    case 53: me.mealFriDinner(true);break;
                    case 61: me.mealSatBreakfast(true);break;
                    case 62: me.mealSatLunch(true);break;
                    case 63: me.mealSatDinner(true);break;
                    case 71: me.mealSunBreakfast(true);break;
                    case 72: me.simpleMeal(true);break;
                }
            });

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
            me.otherAffiliation(attender.otherAffiliation);
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
            me.setAttenderMeals(attender);

            me.setLookupValue(me.specialNeedsTypes(), me.selectedSpecialNeedsType, attender.specialNeedsTypeId);
            me.setLookupValue(me.generationTypes(), me.selectedGenerationType, attender.generationId);
            me.setLookupValue(me.ageGroups(), me.selectedAgeGroup, attender.ageGroupId);
            me.setLookupValue(me.generationTypes(), me.selectedGenerationType, attender.generationId);
            me.setLookupValue(me.creditTypes(),me.selectedCreditType, attender.creditTypeId);
            me.setLookupValue(me.housingTypes(),me.selectedHousingType, attender.housingTypeId);
            me.setLookupValue(me.gradeLevels(),me.selectedGradeLevel, attender.gradeLevel);
            me.setLookupValue(me.timePeriods(),me.selectedArrivalTime, attender.arrivalTime);
            me.setLookupValue(me.timePeriods(),me.selectedDepartureTime, attender.departureTime);
            me.setLookupValue(me.affiliationCodes(),me.selectedAffiliationCode, attender.affiliationCode);
        };

        public setGeneration(generationId : any) {
            var me = this;
            me.setLookupValue(me.generationTypes(), me.selectedGenerationType, generationId);
        }

        public updateAttender = (attender: IAttender) => {
            var me = this;
            attender.attenderId =            me.id();
            attender.changed =               me.changed();
            attender.firstName =             me.firstName();
            attender.lastName =              me.lastName();
            attender.middleName =            me.middleName();
            attender.dateOfBirth =           me.dateOfBirth();
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

            attender.meals = [];
            if (me.mealThursDinner())  {attender.meals.push(43);}
            if (me.mealFriBreakfast()) {attender.meals.push(51);}
            if (me.mealFriLunch())     {attender.meals.push(52);}
            if (me.mealFriDinner())    {attender.meals.push(53);}
            if (me.mealSatBreakfast()) {attender.meals.push(61);}
            if (me.mealSatLunch())     {attender.meals.push(62);}
            if (me.mealSatDinner())    {attender.meals.push(63);}
            if (me.mealSunBreakfast()) {attender.meals.push(71);}
            if (me.simpleMeal())       {attender.meals.push(72);}

            attender.specialNeedsTypeId =  me.getLookupValue(me.selectedSpecialNeedsType);
            attender.generationId = me.getLookupValue(me.selectedGenerationType);
            attender.gradeLevel = me.getLookupValue(me.selectedGradeLevel);
            attender.ageGroupId = me.getLookupValue(me.selectedAgeGroup);
            attender.affiliationCode = me.getLookupValue(me.selectedAffiliationCode);
            attender.creditTypeId = me.getLookupValue(me.selectedCreditType);
            attender.housingTypeId = me.getLookupValue(me.selectedHousingType);

        };


        private setLookupValue(list: IListItem[],  selection : KnockoutObservable<IListItem>, value: any) {
            var result = _.find(list, function(item : IListItem) {
                return item.Value == value;
            });
            selection(result);
        }

        private getLookupValue(selection : KnockoutObservable<IListItem>) {
            var me = this;
            var item = selection();
            return (item) ? item.Value : null;
        }

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;

            var first = me.firstName().trim();
            me.firstName(first);
            var last = me.lastName().trim();
            me.lastName(last);
            if (!first) {
                me.firstNameError(': First name is required.');
                valid = false;
            }

            if (!last) {
                me.lastNameError(': Last name is required.');
                valid = false;
            }

            var arrival = null;
            var departure = null;
            if (me.selectedArrivalTime()) {
                arrival = me.selectedArrivalTime().Value;
            }
            else {
                me.arrivalTimeError(': Arrival time is required. Approximate is ok.');
                valid = false;
            }

            if (me.selectedDepartureTime()) {
                departure = me.selectedDepartureTime().Value;
            }
            else
            {
                me.departureTimeError(': Departure time is required. Approximate is ok.');
                valid = false;
            }

            if (arrival && departure && arrival > departure) {
                me.arrivalTimeError(': Arrival time must precede departure.');
                valid = false;
            }

            var generationId = me.getLookupValue(me.selectedGenerationType);
            if (generationId) {
                if (generationId != 1 && !me.dateOfBirth()) {
                    me.dateOfBirthError(': Date of birth is required for children. Approximate is ok.');
                    valid = false;
                }
            }

            var housingPreference = me.selectedHousingType();
            if (!housingPreference) {
                me.housingPreferenceError(': You must select your housing preference.');
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
        askSecurityQuestion = ko.observable(true);

        public validate = () => {
            var me = this;
            if (me.askSecurityQuestion()) {
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
            }
            me.askSecurityQuestion(false);
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

        // state
        registrationChanged = ko.observable(false);
        attendersChanged = ko.observable(false);
        balanceInvalid : KnockoutComputed<boolean>; // used on accounts form

        private currentRegistration: IRegistrationInfo;
        sessionInfo = new AnnualSessionInfo();
        user = new userObservable();
        registrationStatus = ko.observable(-1);

        // simple forms
        lookupCode = ko.observable('');
        modalInput = ko.observable('');

        // complex forms
        startupForm = new startupFormObservable();
        lookupForm = new lookupFormObservable();
        attenderForm = new attenderObservable();
        registrationForm = new registrationObservable();
        familyMemberResults = new searchListObservable(2,6);
        private familyMembers : IFamilyAttender[] = [];
        addressSearchResults = new searchListObservable(2,6);
        addressSearchValue = ko.observable('');
        addressSearchWarning = ko.observable('');

        // attenders
        attenderList : KnockoutObservableArray<IListItem> = ko.observableArray([]);
        private updatedAttenders : IAttender[] = [];
        private deletedAttenders = [];

        // navigation
        currentForm = ko.observable('');
        saveButtonVisible : KnockoutComputed<boolean>;
        accountReviewed = ko.observable(false);
        registerButton = new regButtonObservable("Summary",'ready');
        contactButton =  new regButtonObservable("Contact");
        attendersButton = new regButtonObservable("Attenders");
        accountButton = new regButtonObservable("Account");

        // Display text
        formTitle = ko.observable('Registration Summary');
        datesText = ko.observable('');
        locationText = ko.observable('');
        housingPreference = ko.observable('');
        housingAssignmentList = ko.observableArray<IListItem>();
        registrationTitle : KnockoutComputed<string>;

        // Constructor
        constructor() {
            var me = this;
            Tops.YmRegistrationViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        saveContactInfo = () => {
            var me = this;
            if (me.validateContactInfo()) {
                me.registrationForm.contactInfoForm.view();
                window.location.assign('#reg-header');
            }
        };

        private checkForUnsavedChanges = ()  => {
            var me = this;

            if (me.registrationForm.id()) {
                return (me.attendersChanged() || me.registrationChanged());
            }
            else {
                var name = me.registrationForm.contactInfoForm.name();
                var lookup = me.registrationForm.registrationCode();
                return (name.trim() && lookup.trim() && me.attendersChanged());
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
            if (me.checkReadyToSave()) {
                me.uploadChanges();
            }
        };

       private uploadChanges() {
            var me = this;
            var request = me.getRegistrationChanges();
            if (!request) {
                me.application.showWarning('No changes were found to save.')
                return;
            }
            me.application.hideServiceMessages();
            me.application.showWaiter('Saving changes...');

           // todo: saveChanges service
            // fakes
           var data = me.getFakeRegistration();
           var fakeResponse = new fakeServiceResponse(data);
           me.handleSaveChangesResponse(fakeResponse);
           me.application.hideWaiter();

            //** end fakes

            /*
             me.peanut.executeService('registration.saveChanges',request, me.handleSaveChangesResponse)
             .always(function() {
             me.application.hideWaiter();
             });
             */

        }

        private handleSaveChangesResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IRegistrationResponse>serviceResponse.Value;
                me.loadRegistration(response);
                me.attendersButton.setStatus('complete');
                me.accountButton.setStatus('complete');
                me.accountReviewed(response.registration.statusId > 1);
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
             jQuery(function() {
                jQuery( ".datepicker" ).datepicker(
                    {
                        changeYear: true,
                        yearRange: 'c-20:c+20'
                    }
                );
             });

            window.onbeforeunload = function() {
                if (me.hasUnsavedChanges()) {
                    if (me.registrationStatus() === 1 && me.attendersButton.isComplete()) {
                        me.accountReviewed(true); // show save button even if we are in 'new registration' wizard mode.
                    }
                    return "**** WARNING: If you reload or leave this page your changes will be lost. ****";
                }
            };

            jQuery('[data-toggle="tooltip"]').tooltip();
            jQuery('[data-toggle="popover"]').popover();

            me.registrationTitle = ko.computed(function() {
                var result = me.registrationForm.contactInfoForm.name();
                result = result ? result.trim() : '';
                return result ? result : 'New Registration';
            });

            me.saveButtonVisible =  ko.computed(function() {
                return ((me.registrationChanged() || me.attendersChanged()) &&
                        (me.registrationStatus() > 1 || me.accountReviewed()));
            });

            me.balanceInvalid = ko.computed(function() {
                if (me.registrationStatus() == 1) {
                    return false;
                }

                if (me.registrationForm.financeInfoForm.calculated() == false) {
                    return true;
                }
                /*
                if (me.registrationForm.contactInfoForm.viewState() == 'edit') {
                    return true;
                }
                if (me.attenderForm.viewState() == 'edit') {
                    return true;
                }
                */
                return (me.attendersChanged() || me.registrationChanged());
            });

            me.application.initialize(applicationPath,
                function() {
                    me.application.loadResources(['scym-registration.css','textParser.js'],
                        function() {
                            me.attenderForm.initialize();
                            me.getInitialInfo(successFunction);
                        }
                    );
                }
            );


            /*
            me.application.loadResources(['scym-registration.css','textParser.js'],
                function() {
                    me.application.initialize(applicationPath,
                        function() {
                            me.attenderForm.initialize();
                            me.getInitialInfo(successFunction);
                        }
                    );
                });
            */
        }

        
        showAddressSearch() {
            var me = this;
            me.addressSearchValue('');
            me.addressSearchResults.reset();
            jQuery("#address-search-modal").modal('show');
        }

        /**
         * On click find address button
         */
        public findAddresses() {
            var me = this;
            me.addressSearchWarning('');
            var request = new KeyValueDTO();
            request.Name = 'Addresses';
            request.Value = me.addressSearchValue();

            if (!request.Value) {
                return;
            }

            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');

            // fakes

            var fakeData : INameValuePair[] = [
                {
                    Name: 'Terry SoRelle and Liz Yeats',
                    Value: '1'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Terry SoRelle and Liz Yeats',
                    Value: '1'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Terry SoRelle and Liz Yeats',
                    Value: '1'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Terry SoRelle and Liz Yeats',
                    Value: '1'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Somebody Lovesme',
                    Value: '2'
                },
                {
                    Name: 'Nobody Whoosits',
                    Value: '2'
                }
            ];

            // fakeData = []; // test

            var fakeResponse = new fakeServiceResponse(fakeData);

            me.application.hideWaiter();
            me.showAddressSearchResults(fakeResponse);

            /* service call
            me.peanut.executeService('directory.DirectorySearch',request, me.showAddressSearchResults)
                .always(function() {
                    me.application.hideWaiter();
                });
            */
        }

        /**
         * Service response handler for findAddresses
         * @param serviceResponse
         */
        public showAddressSearchResults = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.addressSearchResults.setList(list);
                if (list.length == 0) {
                    me.addressSearchWarning('No addresses were found for this name.');
                }
            }
        };

        /**
         * On click item link in found panel
         * @param item
         */
        public selectAddress = (item : INameValuePair) => {
            var me = this;
            me.addressSearchResults.reset();
            me.application.showWaiter('Loading address...');
            me.application.hideServiceMessages();

            // Fakes
            var fakeData : IFindRegistrationAddressResponse = {
                name : 'Terry SoRelle and Liz Yeats',
                address: '904 E. Meadowmere',
                city: 'Austin, TX 78758',
                persons: [
                    {
                        Value: '1',
                        Name: 'Liz Yeats',
                        firstName: 'Liz',
                        lastName: 'Yeats',
                        middleName: 'Rosa',
                        generation: 1,
                        dateOfBirth: null
                    },
                    {
                        Value: '2',
                        Name: 'Terry SoRelle',
                        firstName: 'Terry',
                        lastName: 'SoRelle',
                        middleName: '',
                        generation: 1,
                        dateOfBirth: null
                    },
                    {
                        Value: '3',
                        Name: 'Sam Schifman',
                        firstName: 'Sam',
                        lastName: 'Schifman',
                        middleName: 'Benjamin',
                        generation: 2,
                        dateOfBirth: '6/5/1979'
                    }
                ]
            } ;

            var fakeResponse = new fakeServiceResponse(fakeData);
            me.handleSearchAddressResponse(fakeResponse);
            me.application.hideWaiter();
            jQuery("#address-search-modal").modal('hide');

            /* service call
            var request = item.Value;
            me.peanut.executeService('directory.FindRegistrationAddress',request,me.handleSearchAddressResponse)
                .always(function() {
                    me.application.hideWaiter();
                    jQuery("#address-search-modal").modal('hide');
                });
            */
        };

        private handleSearchAddressResponse = (serviceResponse: IServiceResponse)=> {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var address = <IFindRegistrationAddressResponse>serviceResponse.Value;
                me.familyMembers = address.persons;
                me.familyMemberResults.setList(address.persons);
                me.registrationForm.contactInfoForm.name(address.name);
                me.registrationForm.contactInfoForm.address(address.address);
                me.registrationForm.contactInfoForm.city(address.city);
            }
        };

        public selectFamilyMember = (member : IFamilyAttender) => {
            var me = this;
            me.familyMembers = _.filter(me.familyMembers,function(m: IFamilyAttender){
                return member.Name != m.Name;
            });
            me.familyMemberResults.setList(me.familyMembers);
            me.attenderForm.firstName(member.firstName);
            me.attenderForm.middleName(member.middleName);
            me.attenderForm.lastName(member.lastName);
            me.attenderForm.setGeneration(member.generation);
            if (member.generation != 1) {
                me.attenderForm.dateOfBirth(member.dateOfBirth);
            }
            jQuery("#family-member-modal").modal('hide');
        };

        public endFamilyMemberSelection = () => {
            var me = this;
            jQuery("#family-member-modal").modal('hide');
            me.familyMembers = [];
        };

        public getInitialInfo(successFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Loading...');


            //testing stub...
            /*
            var fakeResponse = me.getFakeInitResponse();
            me.handleInitializationResponse(fakeResponse);
            me.application.hideWaiter();
            if (successFunction) {
                successFunction();
            }
            */
            //****** End fake

            // service call

            var request = null;
            me.peanut.executeService('registration.registrationInit',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    if (successFunction) {
                        successFunction();
                    }
                });
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
               jQuery('#application-container').show();
           }
       };

        private setWelcomeForm() {
            var me = this;
            me.currentForm('welcome');
            me.formTitle('Welcome, begin or continue your registration');
        }


        public getRegistration() {
            var me = this;
            var request = me.user.registrationId();

            me.application.hideServiceMessages();
            me.application.showWaiter('Getting your registration...');

            // ** fake **
            // todo: getRegistration service
            me.fakeRegistrationService();

            /*
            me.peanut.executeService('registration.GetRegistration',request, me.handleServiceResponseTemplate)
                .always(function() {
                    me.application.hideWaiter();
                });
            */
        }

        public findRegistration() {
            var me = this;
            var code = me.lookupForm.getLookupCode();
            if (code) {
                me.application.hideServiceMessages();
                me.application.showWaiter('Finding registration...');

                // ** fake **
                // todo: findByLookupCode service
                me.fakeRegistrationService();

                /*
                 me.peanut.executeService('registration.findByLookupCode',code, me.handleRegistrationResponse)
                 .always(function() {
                 me.application.hideWaiter();
                 });
                 */
            }
        }



        private handleRegistrationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IRegistrationResponse>serviceResponse.Data;
                me.loadRegistration(response);
            }
            else {
                me.lookupForm.validationError('Sorry, cannot locate this registration.')
            }

        };

        private loadRegistration(response : IRegistrationResponse) {
            var me = this;
            me.registrationStatus(response.registration.statusId);
            me.currentRegistration = response.registration;
            me.registrationForm.assign(response.registration);
            me.attenderList(response.attenderList);
            me.registrationForm.financeInfoForm.assignAccountSummary(response.accountSummary);
            me.registrationChanged(false);
            me.attendersChanged(false);
            me.contactButton.setComplete();
            var buttonStatus = response.attenderList.length == 0 ? 'incomplete' : 'complete';
            me.attendersButton.setStatus(buttonStatus);
            me.accountButton.setStatus(buttonStatus);
            me.registrationForm.financeInfoForm.setViewState(response.registration.statusId == 1 ? 'edit':'view');
            me.registrationForm.contactInfoForm.setViewState(response.registration.statusId == 1 ? 'edit':'view');
            me.housingAssignmentList(response.housingAssignments);
            me.housingPreference(response.housingPreference);
            me.accountButton.setStatus(response.registration.confirmed ? 'complete' : 'incomplete');
            me.updatedAttenders = [];
            me.deletedAttenders = [];
            me.showSummaryForm();
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
            me.currentForm('startnew');
            if (me.user.authenticated()) {
                me.lookupForm.lookupCode(me.user.email());
            }
        }


        public startLookup() {
            var me = this;
            if (me.user.authenticated() ||  me.startupForm.validate()) {
                me.lookupForm.clear();
                me.formTitle('Find your registration');
                me.currentForm('lookup');
                if (me.user.authenticated()) {
                    me.lookupForm.lookupCode(me.user.email());
                }
            }
        }

        startNewRegistration() {
            var me = this;
            me.registrationForm.clear();
            me.attenderList([]);
            var code = me.lookupForm.getLookupCode();
            if (code) {
                me.registrationForm.contactInfoForm.edit();
                me.registrationChanged(false);
                me.attendersChanged(false);
                me.registrationForm.registrationCode(code);
                if (Peanut.ValidateEmail(code)) {
                    me.registrationForm.contactInfoForm.email(code);
                }
                me.contactButton.setStatus('incomplete');
                me.registrationStatus(1);
                if (me.user.authenticated) {
                    me.showAddressSearch();
                }
                me.editContactInfo();
            }
        }

        endContactEdit() {
            var me = this;
            var ready = me.validateContactInfo();
            if (!ready) {
                window.location.assign('#contact-errors');
                return false;
            }
            if (me.registrationForm.id()) {
                me.registrationForm.contactInfoForm.view();
            }
            window.location.assign('#reg-header');
            me.contactButton.setStatus('complete');
            return true;
        }

        addAttenders() {
            var me = this;
            var ready = me.endContactEdit();
            if (!ready) {
                return;
            }
            me.attendersButton.setStatus('incomplete');
            me.newAttender();
        }

        saveFinanceInfo = () => {
            var me = this;
            if (!me.registrationForm.financeInfoForm.validate()) {
                window.location.assign('#account-errors');
                return;
            }
            me.registrationChanged(true);
            me.saveChanges();
        };
        
        cancelContactChanges = () => {
            var me = this;
            if (me.registrationStatus() == 1) {
                me.registrationForm.contactInfoForm.clear();
            }
            else {
                me.registrationForm.contactInfoForm.assign(me.currentRegistration);
                me.registrationForm.contactInfoForm.setViewState();
            }
            
        };
        
        cancelFinanceChanges = () => {
            var me = this;
            if (me.registrationStatus() == 1) {
                me.registrationForm.financeInfoForm.clear();
            }
            else {
                me.registrationForm.financeInfoForm.assign(me.currentRegistration);
                me.registrationForm.financeInfoForm.setViewState();
            }
        };

        private newAttender() {
            var me = this;
            me.getAttender(0);
        }

        saveAttenderAddNext() {
            var me = this;
            if (me.saveAttender()) {
                me.newAttender();
            }
        }

        endAddAttenders = () => {
            var me = this;
            me.attenderForm.setViewState();
            if (me.registrationStatus() == 1) {
                me.attendersButton.setComplete();
                me.accountButton.setIncomplete();
                me.registrationForm.financeInfoForm.edit();
                me.showAccountsForm();
            }
        };

        saveAttenderAndDone() {
            var me = this;
            if (me.saveAttender()) {
                me.endAddAttenders();
            }
        }

        cancelAttenderEdit() {
            var me = this;
            var id = me.attenderForm.id();
            me.attenderForm.clear();
            if (me.attenderList().length > 0) {
                me.showAttendersList();
            }
        }

        cancelRegistration() {
            var me = this;
            //todo: implement cancelRegistration
            me.registrationStatus(0);
            me.contactButton.setStatus('inactive');
            me.attendersButton.setStatus('inactive');
            me.accountButton.setStatus('inactive');
        }

        showAccountsForm = () => {
            var me = this;
            if ((!me.registrationForm.financeInfoForm.calculated()) ||  me.balanceInvalid()) {
                me.updateCosts();
            }
            else {
                me.showFeesAndDonationsPage();
            }
        };

        showAccountDetails = () => {
            var me = this;
            me.registrationForm.financeInfoForm.setViewState();
        };

        private showFeesAndDonationsPage() {
            var me = this;
            me.currentForm('accounts');
            me.formTitle('Fees and Donations');
            window.location.assign('#form-column');
        }

        updateCosts = () => {
            var me = this;
            var request : ICostUpdateRequest = {
                ymDonation: me.registrationForm.financeInfoForm.ymDonation(),
                simpleMealDonation: me.registrationForm.financeInfoForm.simpleMealDonation(),
                aidRequested: me.registrationForm.financeInfoForm.aidRequested(),
                attenders: me.updatedAttenders,
                deletedAttenders: me.deletedAttenders
            };
            me.application.hideServiceMessages();
            me.application.showWaiter('Calculating costs...');

            // fakes
            /*
            var fakeData = me.getFakeAccountSummary();
            var fakeResponse = new fakeServiceResponse(fakeData);
            me.handleGetCostResponse(fakeResponse);
            me.application.hideWaiter();
            */
            // todo: GetRegistrationCost service

            me.peanut.executeService('registration.GetRegistrationCost',request, me.handleGetCostResponse)
                .always(function() {
                    me.application.hideWaiter();
                });

        };

        private handleGetCostResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IAccountSummary>serviceResponse.Value;
                me.registrationForm.financeInfoForm.assignAccountSummary(response);
                me.showFeesAndDonationsPage();
            }
        };

        showSummaryForm = () => {
            var me = this;
            me.formTitle("Registration");
            me.currentForm("registration");
            window.location.assign('#reg-header');
        };

        confirmAndSave() {
          var me = this;
            if (!me.registrationForm.financeInfoForm.validate()) {
                window.location.assign('#account-errors');
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

        validateContactInfo() : boolean {
            var me = this;
            var isValid = me.registrationForm.contactInfoForm.validate();
            if (isValid) {
                me.registrationChanged(true);
            }
            else {
                window.location.assign('#contact-errors');
            }
            return isValid;
        }

        hasUnsavedChanges() : boolean {
            var me = this;
            if (me.registrationStatus() == 1) {
                return true;
            }
            if (me.registrationForm.financeInfoForm.viewState() == 'edit') {
                return true;
            }
            if (me.registrationForm.contactInfoForm.viewState() == 'edit') {
                return true;
            }
            if (me.attenderForm.viewState() == 'edit') {
                return true;
            }
            return (me.registrationChanged() || me.attendersChanged());
        }

        checkReadyToSave() {
            var me = this;
            if (me.registrationForm.financeInfoForm.viewState() == 'edit') {
                if (me.registrationForm.financeInfoForm.validate()) {
                    me.registrationForm.financeInfoForm.setViewState();
                }
                else {
//                    me.application.showError('Please complete this form');
                    window.location.assign('#account-errors');
                    me.showAccountsForm();
                    return false;
                }
            }
            if (me.registrationForm.contactInfoForm.viewState() == 'edit') {
                if (me.validateContactInfo()) {
                    me.registrationForm.contactInfoForm.setViewState();
                }
                else {
                    me.application.showError('Please complete the contact information form.');
                    me.showContactForm();
                    return false;
                }
            }
            if (me.attenderForm.viewState() == 'edit') {
                me.application.showError('Please complete the attender form.');
                me.showAttenderForm();
                return false;
            }

            if (me.attenderList().length == 0) {
                me.application.showError('You must add at least one attender');
                me.showAttenderForm();
                return false;
            }

            return true;

        }

        editSelectedAttender = (item: IListItem) => {
            var me = this;
            var id = item.Value;
            var attender = me.getAttender(id);
        };

        removeSelectedAttender = (item: IListItem) => {
            var me = this;
            me.attendersChanged(true);
            var id = item.Value;
            var attenderList = me.attenderList();
            attenderList = _.filter(attenderList,function(attenderItem: IListItem) {
                return attenderItem.Value != id;
            });
            me.attenderList(attenderList);
            var updated = _.filter(me.updatedAttenders, function(attender: IAttender) {
                return attender.attenderId != id;
            });
            me.updatedAttenders = updated;
            if (id > 0) {
                me.deletedAttenders.push(id);
            }
        };

        uncancel = () => {
            var me = this;
            // todo: implement uncancel
            alert('Uncancel not implemented yet.')
        };

        saveAttender() {
            var me = this;
            me.attendersChanged(true);
            if (!me.attenderForm.validate()) {
                window.location.assign('#attender-errors');
                return false;
            }

            var onUpdateList = false;
            var attender  = null;
            var id = me.attenderForm.id();
            var isNew = (!id);
            if (isNew) {
                id = me.getTempAttenderId();
                me.attenderForm.id(id);
            }
            else {
                attender = _.find(me.updatedAttenders,function(a : IAttender){
                    return a.attenderId == id;
                });
                onUpdateList = (attender) ? true : false;
            }

            if (attender == null) {
                attender = {};
            }
            me.attenderForm.updateAttender(<IAttender>attender);

            if (isNew) {
                me.attenderList.push(
                    {
                        Text: me.attenderForm.attenderFullName(),
                        Value: id,
                        Description: ''
                    }
                );
            }

            if (!onUpdateList) {
                me.updatedAttenders.push(attender);
            }

            if (me.accountButton.isComplete()) {
                me.accountReviewed(true);
            }
            return true;
        }

        getTempAttenderId() {
            var me = this;
            var result = 0;
            _.each(me.updatedAttenders,function(attender: IAttender) {
                if (result > attender.attenderId) {
                    result = attender.attenderId;
                }
            });
            return result - 1;
        }


        getAttender(attenderId: any) {
            var me = this;
            var attender : IAttender = null;

            if (attenderId) {
                attender = _.find(me.updatedAttenders, function (item:IAttender) {
                    return item.attenderId == attenderId;
                });
                if (attender) {
                    me.editAttender(attender);
                    return;
                }
            }
            else {
                if (me.attenderForm.lookupsAssigned) {
                    me.editAttender();
                    return;
                }
            }

            me.application.hideServiceMessages();
            me.application.showWaiter('Getting attender data...');

            var request : IGetAttenderRequest = {
                id: attenderId,
                includeLookups: me.attenderForm.lookupsAssigned ? 1 : 0
            };

            // todo: getAttender service
            var fakeAttenderResponse  = {
                attender: me.getFakeAttender(attenderId),
                lookups : me.getFakeLookups()
            };
            var fakeResponse = new fakeServiceResponse(fakeAttenderResponse);
            me.application.hideWaiter();
            me.handleGetAttenderResponse(fakeResponse);


            /*
            me.peanut.executeService('registration.getAttender',request, me.handleGetAttenderResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
                */
        }

        private handleGetAttenderResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetAttenderResponse>serviceResponse.Value;
                if (response) {
                    if (response.lookups) {
                        me.attenderForm.assignLookupLists(response.lookups);
                    }
                }
                me.editAttender(response.attender);
            }
        };


        private editAttender(attender: IAttender = null) {
            var me = this;
            if (attender) {
                me.attenderForm.assign(attender);
                me.formTitle("Update attender: " + textParser.makeFullName(attender.firstName, attender.lastName, attender.middleName));
            }
            else {
                me.attenderForm.clear();
                me.attenderForm.setDefaults(
                    me.updatedAttenders.length > 0 ? me.updatedAttenders[0] : null
                );
                me.formTitle('New Attender');
            }
            me.attenderForm.edit();
            window.location.assign('#reg-header');
            me.showAttenderForm();
        }

        showWelcomeForm() {
            var me = this;
            me.currentForm('welcome');
        };
        showAttendersList = () => {
            var me = this;
            var me = this;
            me.attenderForm.setViewState();
            me.formTitle('Attenders');
            me.showAttenderForm();
        };

        showAttenderForm = () => {
            var me = this;
            me.currentForm('attenders');
            window.location.assign('#reg-header');
            if (me.attenderForm.id() == 0 && me.familyMembers.length > 0) {
                jQuery("#family-member-modal").modal('show');
            }
        };

        showContactForm = () => {
            var me = this;
            me.formTitle('Contact Information');
            me.currentForm('contact');
            window.location.assign('#reg-header');
        };

        editContactForm = () => {
            var me = this;
            me.registrationForm.contactInfoForm.edit();
        };

        editAccountsForm = () => {
            var me = this;
            me.registrationForm.financeInfoForm.edit();
            me.showAccountsForm();
        };


        private editContactInfo(){
            var me = this;
            me.registrationForm.contactInfoForm.edit();
            me.showContactForm();
        }


        getRegistrationChanges() {
            var me = this;
            if (me.registrationChanged() || me.attendersChanged()) {
                var request : IRegistrationUpdateRequest =  {
                    registration : null,
                    updatedAttenders : me.updatedAttenders,
                    deletedAttenders : me.deletedAttenders,
                };
                if (me.registrationChanged()) {
                    request.registration = <IRegistrationInfo>{};
                    me.registrationForm.updateRegistration(<IRegistrationInfo>request.registration);
                }
                return request;
            }
            return null;
        }

        sendRegistrarMessage = () => {
            var me = this;
            if (me.modalInput()) {
                // todo: implement send registrar message
                alert('Send message not implemented yet.');
                me.modalInput('');
                jQuery("#registrar-contact-modal").modal('hide');
            }
        };


        showRegistrarContactForm() {
            var me = this;
            me.modalInput('');
            jQuery("#registrar-contact-modal").modal('show');
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

        private fakeRegistrationService(isConfirmed = 1) {
            var me = this;
            var data = me.getFakeRegistration(isConfirmed);
            var fakeResponse = new fakeServiceResponse(data);
            me.handleRegistrationResponse(fakeResponse);
            me.application.hideWaiter();
        }

        private getFakeRegistration(isConfirmed = 0) {
            var me = this;
            var accountSummary : IAccountSummary = me.getFakeAccountSummary();
            var result : IRegistrationResponse = {
                attenderList: [
                    {
                        Text: 'Terry SoRelle',
                        Value: '1',
                        Description: ''
                    },
                    {
                        Text: 'Liz Yeats',
                        Value: '2',
                        Description: ''
                    },
                    {
                        Text: 'Sam Schifman',
                        Value: '3',
                        Description: ''
                    }
                ],
                registration: {
                    registrationId: 1,
                    confirmed: isConfirmed,
                    active: 1,
                    year: '2015',
                    registrationCode: 'ABCD',
                    statusId: 2,
                    name: 'Terry SoRelle and Liz Yeats',
                    address: '904 E. Meadowmere',
                    city: 'Austin, TX 78758',
                    phone: '532-029-0292',
                    email: 'terry@mail.com  ',
                    receivedDate: '10/1/2015',
                    amountPaid: '0.00',
                    notes: 'Notes here',
                    feesReceivedDate: null,
                    arrivalTime: '42',
                    departureTime: '71',
                    scymNotes: '',
                    statusDate: '10/1/2015',
                    ymDonation: '',
                    simpleMealDonation: '',
                    aidRequested: '',
                },
                accountSummary: accountSummary,
                housingPreference: 'Health House',
                housingAssignments: [
                    {
                        Text: 'Liz, Terry',
                        Value: 'Health House 10',
                        Description: 'Friday night through Saturday night'
                    },
                    {
                        Text: 'Sam',
                        Value: 'Young Friends Dorm',
                        Description: 'Thursday night through Saturday night'
                    }
                ]
            };

            return result;
        }

        private getFakeAccountSummary() : IAccountSummary {
            var result : IAccountSummary =
            {
                payments: [],
                fees: [
                    {
                        Text: 'Registration fee',
                        Value: '$55.00',
                        Description: 'Terry'
                    },
                    {
                        Text: 'Registration fee',
                        Value: '$55.00',
                        Description: 'Liz'
                    },
                    {
                        Text: 'Adult Lodging and meals three nights',
                        Value: '$195.00',
                        Description: 'Terry 3 nights'
                    },
                    {
                        Text: 'Adult Lodging and meals three nights',
                        Value: '$195.00',
                        Description: 'Terry 3 nights'
                    }
                ],
                credits: [
                    {
                        Text: 'Financial aid',
                        Value: '$20.00',
                        Description: ''
                    }
                ],
                donations: [
                    {
                        Text: 'Simple meal',
                        Value: '$20.00',
                        Description: ''
                    },
                    {
                        Text: 'General',
                        Value: '$30.00',
                        Description: ''
                    },
                ],
                feeTotal: '$365.00',
                creditTotal: '$150.00',
                donationTotal: '$50',
                balance: '265'
            };

            return result;
        }

        private getFakeAttender(attenderId : any) {
            var me = this;
            if (!attenderId) {
                return null;
            }

            var attenderStub = _.find(me.attenderList(), function(item : IListItem) {
                return item.Value == attenderId;
            });

            if (!attenderStub) {
                return null;
            }

            var result : IAttender = {
                attenderId: attenderStub.Text,
                firstName : '',
                lastName : attenderStub.Value,
                middleName : '',
                dateOfBirth : null,
                affiliationCode : '',
                otherAffiliation : '',
                firstTimer : 0,
                teacher : 0,
                financialAidRequested : 0,
                guest : 0,
                notes : '',
                linens : 0,
                arrivalTime  : '',
                departureTime  : '',
                vegetarian : 0,
                attended : 0,
                singleOccupant : 0,
                glutenFree : 0,
                changed: false,
                housingTypeId : null,
                specialNeedsTypeId : null, // lookup: special needs
                generationId : null, // lookup: generations
                gradeLevel : '', // 'PS','K', 1 .. 13
                ageGroupId : null, // lookup agegroups
                creditTypeId : 0, // formerly: feeCredit, lookup: creditTypes
                meals: []
            };
            return result;
        }

        private getFakeLookups() : IAttenderLookups {
            var attenderLists  = {
                housingTypes:
                    [
                        { Text:'Day visitor - no housing',Value:'1',Description:'', category: '1' },
                        { Text:'Early Riser Dorm for Women',Value:'2',Description:'', category: '1' },
                        { Text:'Night Owl Dorm for Women',Value:'3',Description:'', category: '1' },
                        { Text:'Early Riser Dorm for Men',Value:'4',Description:'' ,category: '1'},
                        { Text:'Night Owl Dorm for Men',Value:'5',Description:'' ,category: '1'},
                        { Text:'Family Cabin',Value:'6',Description:'' ,category: '1'},
                        { Text:'Solo Parent Cabin (not used)',Value:'7',Description:'' ,category: '1'},
                        { Text:'Couples Cabin',Value:'8',Description:'' ,category: '1'},
                        { Text:'Camp Motel',Value:'9',Description:'' ,category: '3'},
                        { Text:'Health House (special needs)',Value:'10',Description:'' ,category: '3'},
                        { Text:'Tenting',Value:'11',Description:'' ,category: '1'},
                        { Text:'Adult Young Friends Dorm',Value:'12',Description:'' ,category: '1'},
                        { Text:'Female Young Frends Dorm',Value:'13',Description:'' ,category: '1'},
                        { Text:'Male Young Friends Dorm',Value:'14',Description:'' ,category: '1'},
                        { Text:'Jr. Young Friends Female',Value:'16',Description:'' ,category: '1'},
                        { Text:'Jr. Young Friends Male',Value:'17',Description:'' ,category: '1'},
                        { Text:'Mothers with Children Dorm',Value:'18',Description:'' ,category: '1'},
                        { Text:'Fathers with Children Dorm',Value:'19',Description:'' ,category: '1'}
                    ],
                affiliationCodes :
                    [
                        { Text:'Acadiana Friends Meeting',Value:'ACDN',Description:'' },
                        { Text:'Alpine Friends Worship Group',Value:'AL TX',Description:'' },
                        { Text:'Friends Meeting of Austin',Value:'AU TX',Description:'' },
                        { Text:'Baton Rouge Friends Meeting',Value:'BA LA',Description:'' },
                        { Text:'Caddo Four States Preparatory Meeting',Value:'CADDO4',Description:'' },
                        { Text:'Coastal Bend Friends Meeting',Value:'CC TX',Description:'' },
                        { Text:'College Station Friends Worship Group',Value:'CS TX',Description:'' },
                        { Text:'Dallas Monthly Meeting of Friends',Value:'DA TX',Description:'' },
                        { Text:'Fayetteville Friends',Value:'FA AR',Description:'' },
                        { Text:'Fort Worth Monthly Meeting',Value:'FW TX',Description:'' },
                        { Text:'Galveston Friends Meeting',Value:'GA TX',Description:'' },
                        { Text:'Hill Country Friends Monthly Meeting',Value:'HC TX',Description:'' },
                        { Text:'Houston Live Oak Friends Meeting',Value:'HO TX',Description:'' },
                        { Text:'Little Rock Friends Meeting',Value:'LR AR',Description:'' },
                        { Text:'Lubbock Monthly Meeting',Value:'LU TX',Description:'' },
                        { Text:'Norman Friends Meeting',Value:'NFS',Description:'' },
                        { Text:'Friends Meeting of New Orleans',Value:'NO LA',Description:'' },
                        { Text:'No SCYM affiliation',Value:'NONE',Description:'' },
                        { Text:'Oklahoma City Friends Meeting',Value:'OKC',Description:'' },
                        { Text:'Rio Grande Valley Worship Group',Value:'RIO',Description:'' },
                        { Text:'Friends Meeting of San Antonio',Value:'SA TX',Description:'' },
                        { Text:'Stillwater Friends Meeting',Value:'ST OK',Description:'' },
                        { Text:'Sunrise Friends Meeting',Value:'SUN',Description:'' },
                        { Text:'Tulsa Green Country',Value:'TU OK',Description:'' }
                    ],
                ageGroups : 
                    [
                        { Text:'Little Friends',Value:'1',Description:'' },
                        { Text:'Lower Elementary',Value:'2',Description:'' },
                        { Text:'Upper Elementary',Value:'3',Description:'' },
                        { Text:'Junior High',Value:'4',Description:'' },
                        { Text:'High School',Value:'5',Description:'' }
                    ]
            };

            return <IAttenderLookups>attenderLists;
        }

        private getFakeInitResponse() {
            var loggedIn =
                // false;
                true;


            var fakeSessionInfo: IAnnualSessionInfo = {
                year : '2015',
                startDate: '2015-04-02',
                endDate : '2015-05-05',
                location: 'Greene Family Camp, Bruceville, Texas',
                datesText: 'April 2nd to 5th, 2015'
            };

            return loggedIn ?
                new fakeServiceResponse(
                    {
                        sessionInfo: fakeSessionInfo,
                        registrationId: 1,
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
        }
    }
}

Tops.YmRegistrationViewModel.instance = new Tops.YmRegistrationViewModel();
(<any>window).ViewModel = Tops.YmRegistrationViewModel.instance;