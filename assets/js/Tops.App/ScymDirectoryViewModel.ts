/**
 * Created by Terry on 7/13/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />
/// <reference path='../components/editPanel.ts' />
/// <reference path='../components/searchListObservable.ts' />

module Tops {
    /**
     * Person DTO as returned from services
     */
    export class scymPerson {
        public personId : any = null; // database entity id
        public firstName='';
        public middleName='';
        public lastName='';
        public username : string = '';
        public addressId : any = null;
        public phone : string = '';
        public phone2 : string = '';
        public email : string = '';
        public newsletter : number = 0;
        public dateOfBirth : string = '';
        public notes : string = '';
        public junior: number = 0;
        public active: number = 1;
        public sortkey : string = '';
        public affiliationcode : string = '';
        public memberaffiliation : string = '';
        // public membershiptypeid : any = null;
        public otheraffiliation : string = '';
        public directorylistingtypeid: number=1;
        public lastUpdate : string = '';
        public organization: string = '';

        public editState : number = editState.created;
    }

    /**
     * address DTO as returned from services
     */
    export class scymAddress {
        public addressId : any = null; // database entity id
        public addressname = '';
        public addressTypeId : number = 1;
        public address1 = '';
        public address2 = '';
        public city = '';
        public state = '';
        public postalcode = '';
        public country = '';
        public phone = '';
        public notes = '';
        public newsletter : number = 1;
        public active : number = 1;
        public sortkey = '';
        public directorylistingtypeid: number=1;
        public lastUpdate : string = '';

        public id : string = ''; // client side id
        public editState: number = editState.created;
    }

    export class addressPersonServiceRequest {
        personId: any;
        addressId: any;
    }

    export class newPersonForAddressRequest {
        person: scymPerson;
        addressId: any;
    }

    export class newAddressForPersonRequest {
        personId: any;
        address: scymAddress;
    }



    /**
     * Related persons and address DTO as returned by service
     */
    export interface IScymFamily {
        address : scymAddress;
        persons: scymPerson[];
        selectedPersonId : any;
    }

    export interface IInitDirectoryResponse {
        canEdit : boolean;
        directoryListingTypes : INameValuePair[];
        affiliationCodes : INameValuePair[];
        // membershipTypes : INameValuePair[];
        family : IScymFamily;
    }


    /**
     * Local structure to track related persons and addresses and observables
     */
    class clientFamily {
        public address : scymAddress;
        public persons: scymPerson[] = [];
        public selectedPersonId : any = null;
        private newId : number = 0;
        public changeCount:number = 0;

        public visible = ko.observable(false);
        public hasAddress = ko.observable(false);
        public personCount = ko.observable(0);


        /**
         * assign family object
         * @param family
         * @returns {scymPerson} first person in list
         */
        public setFamily(family: IScymFamily){
            var me = this;
            me.setAddress(family.address);
            var selected = me.setPersons(family.persons,family.selectedPersonId);

            return selected;
        }
        
        public empty() {
            var me = this;
            me.address = null;
            me.hasAddress(false);
            me.persons = [];
            me.personCount(0);
            me.selectedPersonId = 0;
        }

        /**
         * assign address object
         * @param address
         */
        public setAddress(address: scymAddress) {
            var me = this;
            me.address = address;
            me.hasAddress(address != null);
        }

        /**
         * Set null address
         */
        public clearAddress(selectedPersonId : any = 0) {
            var me = this;
            me.address = null;
            me.hasAddress(false);
            var person = null;
            if (selectedPersonId) {
                person = me.selectPerson(selectedPersonId);
            }
            else {
                person = me.getSelected();
            }

            me.persons = [];

            if (person == null) {
                me.visible(false);
            }
            else {
                me.persons.push(person);
            }
        }

        /**
         * Return non-deleted persons from list
         * @returns {scymPerson[]}
         */
        private getActivePersons() : scymPerson[] {
            var me = this;
            var result = _.filter(me.persons, function(person: scymPerson){
                return person.editState != editState.deleted;
            });
            return result;
        }

        /**
         * Make first person on list the selected person
         * @returns {scymPerson} (null if no persons)
         */
        public selectFirstPerson() : scymPerson {
            var me = this;
            var active = me.getActivePersons();
            var firstPerson = _.first(active);
            if (firstPerson) {
                me.selectedPersonId = firstPerson.personId;
            }
            else {
                me.selectedPersonId = 0;
            }
            return <scymPerson>firstPerson;
        }



        /**
         * Assign persons list
         * @param persons
         * @returns {scymPerson}
         */
        public setPersons(persons: scymPerson[], selectedPersonId: any = 0) : scymPerson {
            var me = this;
            me.personCount(0);

            var selectedPerson : scymPerson = null;

            if (persons) {
                _.each(persons,function(person: scymPerson){
                    person.editState = editState.unchanged;
                });

                me.persons = persons;
                if (selectedPersonId) {
                    selectedPerson = me.getPersonById(selectedPersonId);
                }

                if (!selectedPerson) {
                    selectedPerson = me.selectFirstPerson();
                }
            }
            else {
                me.persons = [];
            }

            me.personCount(persons.length);
            me.selectedPersonId = selectedPerson ? selectedPerson.personId : null;
            return selectedPerson;
        }

        public addPersonToList(person: scymPerson, selected: boolean = true) {
            var me = this;
            var i = _.findIndex(me.persons, function(thePerson: scymPerson) {
                return thePerson.personId == person.personId;
            },me);
            if (i > 0) {
                me.persons[i] = person;
            }
            else {
                me.persons.push(person);
                me.personCount(me.persons.length);
            }
            if (selected) {
                me.selectedPersonId = person.personId;
            }

        }

        /**
         * Return selected person object
         * @returns {scymPerson}
         */
        public getSelected() : scymPerson {
            var me = this;

            var selected = _.find(me.persons,function(person) {
                return me.selectedPersonId == person.personId;
            },me);
            return <scymPerson>selected;
        }

        public getPersonById(id : any) : scymPerson {
            var me = this;
            if (!id) {
                return null;
            }

            var result = _.find(me.persons,function(person) {
                return id == person.personId;
            },me);
            return <scymPerson>result;

        }

        /**
         * Set selected person by id
         * @param id
         * @returns {scymPerson}
         */
        public selectPerson(id : any) : scymPerson {
            var me = this;
            var selected = null;
            if (id) {
                selected = _.find(me.persons,function(person) {
                    return person.personId == id;
                },me);
                if (selected) {
                    me.selectedPersonId = id;
                }
            }
            else {
                me.selectedPersonId = null;
            }
            return <scymPerson>selected;
        }


        /**
         * Set deleted flag on person for id
         * @param id
         * @returns {scymPerson} (person deleted)
         */
        public removePerson(personId : string) {
            var me = this;
            // remove from array
            var currentPersons = me.persons;
            me.persons =  _.reject(currentPersons, function(person: scymPerson){
                return person.personId == personId;
            });

            var selected = me.selectFirstPerson();

            return selected;
        }

        public isLoaded = () => {
            var me = this;
            return (me.address != null || me.persons.length > 0);
        }

    }

    export class saveOperations {
        static update = 'update';
        static insert = 'insert';
        static reassign = 'reassign';
    }

    export class directoryEditPanel extends editPanel {
        public searchList = new searchListObservable(2,10);
        public directoryListingTypeId = ko.observable(1);
        public selectedDirectoryListingType : KnockoutObservable<INameValuePair> = ko.observable(null);
        public directoryListingTypes = ko.observableArray<INameValuePair>([]);
        protected getDirectoryListingItem = () => {
            var me = this;
            var lookup = me.directoryListingTypes();
            var id = me.directoryListingTypeId();
            if (!id) {
                id = 0;
            }
            var key = id.toString();

            var result = _.find(lookup,function(item : INameValuePair) {
                return item.Value == key;
            },me);
            return result;
        };

    }

    /**
     * observable container for person panel
     */
    export class personObservable extends directoryEditPanel {

        public personId = ko.observable('');
        public firstName = ko.observable('');
        public middleName = ko.observable('');
        public lastName = ko.observable('');
        public username = ko.observable('');
        public phone = ko.observable('');
        public phone2 = ko.observable('');
        public email = ko.observable('');
        public newsletter = ko.observable(0);
        public dateOfBirth = ko.observable('');
        public notes = ko.observable('');
        public junior= ko.observable(0);
        public active= ko.observable(1);
        public sortkey = ko.observable('');
        public affiliationcode = ko.observable('');
        public memberaffiliation = ko.observable('');

        public otheraffiliation = ko.observable('');
        public lastUpdate = ko.observable('');
        public organization = ko.observable('');

        public selectedAffiliation : KnockoutObservable<INameValuePair> = ko.observable(null);
        public affiliations = ko.observableArray<INameValuePair>([]);
        public selectedMembershipAffiliation : KnockoutObservable<INameValuePair> = ko.observable(null);

        public affiliation : KnockoutComputed<string>;
        public membership : KnockoutComputed<string>;
        public hasAffiliation : KnockoutComputed<boolean>;
        public hasMembership : KnockoutComputed<boolean>;
        public emailLink : KnockoutComputed<string>;

        // public directoryListing: KnockoutComputed<string>;

        public firstNameError = ko.observable('');
        public lastNameError = ko.observable('');
        public emailError = ko.observable('');
        public affiliationError = ko.observable('');
        public showOutsideMeeting = ko.observable(false);
        public membershipType = ko.observable('');
        private ignoreTriggers = false;


        constructor() {
            super();
            var me = this;
            // me.directoryListing = ko.computed(me.computeDirectoryListing);
            // me.membershipType = ko.computed(me.computeMembershipType);
            me.affiliation = ko.computed(me.computeAffiliation);
            me.membership = ko.computed(me.computeMembership);
            me.hasAffiliation = ko.computed(me.computeHasAffiliation);
            me.hasMembership = ko.computed(me.computeHasMembership);
            me.emailLink = ko.computed(me.computeEmailLink);
            me.selectedAffiliation.subscribe(me.onAffiliationCodeSelected);
            me.selectedMembershipAffiliation.subscribe(me.onMemberAffiliationSelected);
            me.membershipType.subscribe(me.onMembershiptypeChanged);
        }

        /*
        computeDirectoryListing = () => {
            var me = this;
            var lookup = me.directoryListingTypes();
            var key = me.directorylistingtypeid().toString();
            var result = _.find(lookup,function(item : INameValuePair) {
                return item.Value == key;
            },me);
            return result ? result.Name : '';
        };
        */


        private getAffiliationItem = (key: string) => {
            var me = this;
            var lookup = me.affiliations();
            var result = _.find(lookup,function(item : INameValuePair) {
                return item.Value == key;
            },me);
            return result;
        };

        onAffiliationCodeSelected = (selected : INameValuePair) => {
            var me = this;
            if (!me.ignoreTriggers) {
                if (selected) {
                    me.affiliationcode(selected.Value);
                }
                else {
                    me.affiliationcode('');
                }
                me.setMembershipType();

            }
        };

        onMemberAffiliationSelected = (selected : INameValuePair) => {
            var me = this;
            if (!me.ignoreTriggers) {
                if (selected) {
                    me.memberaffiliation(selected.Value);
                }
                else {
                    me.memberaffiliation('');
                }
                me.setMembershipType();

            }
        };

        onMembershiptypeChanged = (value : string) => {
            var me = this;
            if (!me.ignoreTriggers) {
                if (value == 'attender') {
                    me.setMembershipAffiliation('NONE');
                }
                else if (value == 'member') {
                    var attending = me.affiliationcode();
                    me.setMembershipAffiliation(attending);
                }
            }
        };

        setMembershipAffiliation(value : string) {
            var me = this;
            me.ignoreTriggers = true;
            var item = me.getAffiliationItem(value);
            me.selectedMembershipAffiliation(item);
            me.memberaffiliation(value);
            me.ignoreTriggers = false;
        }

        setMembershipType = () => {
            var me = this;
            var type = me.getMembershipType();
            me.ignoreTriggers = true;
            me.membershipType(type);
            me.ignoreTriggers = false;
        };

        getMembershipType() {
            var me = this;
            var attender = me.affiliationcode();
            var member = me.memberaffiliation();
            if (member == 'OTHER' || attender=='OTHER') {
                me.showOutsideMeeting(true);
                return 'other';
            }
            me.showOutsideMeeting(false);
            if (attender == 'NONE' || attender == '') {
                return '';
            }
            if (member == 'NONE' || member == '') {
                return 'attender';
            }
            if (member == attender) {
                return 'member';
            }
            return 'other';
        }

        computeAffiliation = () => {
            var me = this;
            var key = me.affiliationcode();
            var result = me.getAffiliationItem(key);
            return result ? result.Name : '';
        };

        computeMembership = () => {
            var me = this;
            var key = me.memberaffiliation();
            var result = me.getAffiliationItem(key);
            return result ? result.Name : '';
        };

        computeHasAffiliation = () => {
            var me = this;
            var code = me.affiliationcode();
            if (code == 'NONE' || code === '' || code === null ) {
                return false;
            }
            return true;
        };

        computeHasMembership = () => {
            var me = this;
            var code = me.memberaffiliation();
            if (code == 'NONE' || code === '' || code === null ) {
                return false;
            }
            return true;
        };

        computeEmailLink = () => {
            var me = this;
            var email = me.email();
            return email ? 'mailto:' + me.fullName() + '<' + email + '>' : '#';
        };

        /**
         * reset fields
         */
        public clear() {
            var me=this;
            me.isAssigned = false;
            me.clearValidations();
            me.firstName('');
            me.middleName('');
            me.lastName('');
            me.username('');
            me.phone('');
            me.phone2('');
            me.email('');
            me.newsletter(0);
            me.dateOfBirth('');
            me.notes('');
            me.junior(0);
            me.active(1);
            me.sortkey('');
            me.affiliationcode('');
            me.memberaffiliation('');

            me.otheraffiliation('');
            me.directoryListingTypeId = ko.observable(1);
            me.lastUpdate('');
            me.personId('');
            me.organization('');
            me.selectedAffiliation(null);
            me.selectedMembershipAffiliation(null);
            me.membershipType('');
        }

        public clearValidations() {
            var me = this;
            me.firstNameError('');
            me.lastNameError('');
            me.emailError('');
            me.affiliationError('');
            me.hasErrors(false);
        }

        /**
         * set fields from person DTO
         */
        public assign = (person: scymPerson) => {
            var me=this;
            if (!person) {
                me.clear();
                return;
            }
            me.isAssigned = true;
            me.clearValidations();
            me.firstName(person.firstName);
            me.middleName(person.middleName);
            me.lastName(person.lastName);
            me.username(person.username);
            me.phone(person.phone);
            me.phone2(person.phone2);
            me.email(person.email);
            me.newsletter(person.newsletter);
            me.dateOfBirth(person.dateOfBirth);
            me.notes(person.notes);
            me.junior(person.junior);
            me.active(person.active);
            me.sortkey(person.sortkey);
            me.affiliationcode(person.affiliationcode);
            me.memberaffiliation(person.memberaffiliation);
            me.otheraffiliation(person.otheraffiliation);
            me.directoryListingTypeId(person.directorylistingtypeid);
            me.lastUpdate(person.lastUpdate);
            me.personId(person.personId);
            me.organization(person.organization);
            var affiliationItem = me.getAffiliationItem(person.affiliationcode);
            me.selectedAffiliation(affiliationItem);
            affiliationItem = me.getAffiliationItem(person.memberaffiliation);
            me.selectedMembershipAffiliation(affiliationItem);
            var directoryListingItem = me.getDirectoryListingItem();
            me.selectedDirectoryListingType(directoryListingItem);
            me.setMembershipType();
        };

        public updateScymPerson = (person: scymPerson) => {
            var me = this;

            person.active = me.active();
            var affiliation = me.selectedAffiliation();
            if (affiliation) {
                me.affiliationcode(affiliation.Value);
            }
            person.affiliationcode = me.affiliationcode();

            affiliation = me.selectedMembershipAffiliation();
            var membershipAffiliationCode = affiliation ? affiliation.Value : 'NONE';
            if (affiliation) {
                me.memberaffiliation(membershipAffiliationCode);
            }
            person.memberaffiliation = membershipAffiliationCode;

            var listingType = me.selectedDirectoryListingType();
            if (listingType) {
                var listingCode = listingType.Value ? Number(listingType.Value) : 0;
                me.directoryListingTypeId(listingCode);
            }

            person.directorylistingtypeid = me.directoryListingTypeId();
            person.personId = me.personId();
            person.dateOfBirth = me.dateOfBirth();
            if (person.dateOfBirth) {
                me.junior(0);
            }
            person.junior = me.junior();
            person.email = me.email();
            person.firstName = me.firstName();
            person.lastName = me.lastName();
            person.middleName = me.middleName();
            person.newsletter = me.newsletter();
            person.notes = me.notes();
            person.otheraffiliation = me.otheraffiliation();
            person.phone = me.phone();
            person.phone2 = me.phone2();
            person.sortkey = me.sortkey();
            person.username = me.username();
            person.organization = me.organization();
        };

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;
            var value = me.firstName();
            if (!value) {
                me.firstNameError(": Please enter the first name");
                valid = false;
            }
            value = me.lastName();
            if (!value) {
                me.lastNameError(": Please enter the last name");
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
            value = me.affiliationcode();
            if (!value) {
                me.affiliationError(': Please select the attended meeting, or "None".');
                valid = false;
            }

            me.hasErrors(!valid);
            return valid;
        };

        /**
         * format full name from parts
         */
        public static makeFullName(first: string, middle: string, last:string) {
            var result = first.trim();
            if (middle) {
                result = result + ' ' + middle.trim();
            }
            if (last){
                result = result + ' ' + last.trim();
            }
            return result;
        }

        /**
         * return full name based on component name values
         */
        public fullName = () => {
            var me = this;
            var first = this.firstName();
            var middle = this.middleName();
            var last = this.lastName();
            return personObservable.makeFullName(first,middle,last);
        }


    }

    /**
     * observable container for address panel
     */
    export class addressObservable extends directoryEditPanel {

        public addressId : KnockoutObservable<any> =  ko.observable();
        public addressname= ko.observable('');
        public address1= ko.observable('');
        public address2= ko.observable('');
        public addressTypeId = ko.observable(1);
        public city= ko.observable('');
        public state= ko.observable('');
        public postalcode= ko.observable('');
        public country= ko.observable('');
        public phone= ko.observable('');
        public notes= ko.observable('');
        public active  = ko.observable(1);
        public sortkey= ko.observable('');
        public lastUpdate = ko.observable('');
        public newsletter = ko.observable(0);
        public cityLocation : KnockoutComputed<string>;
        public addressNameError = ko.observable('');

        constructor() {
            super();
            var me = this;
            me.cityLocation = ko.computed(me.computeCityLocation);
        }

        public search() {
            var me = this;

            me.viewState('search');
        }



        computeCityLocation = ()  => {
            var me = this;
            var city = me.city();
            var state = me.state();
            var zip = me.postalcode();

            var result = city ? city : '';

            if (result) {
                if (state) {
                    result = result + ', ';
                }
            }
            if (state) {
                result = result + state;
            }

            if (zip) {
                result = result + ' ' + zip;
            }

            return result;
        };

        /**
         * reset fields
         */
        public clear() {
            var me = this;
            me.isAssigned = false;
            me.clearValidations();
            me.addressname('');
            me.address1('');
            me.address2('');
            me.city('');
            me.state('');
            me.postalcode('');
            me.country('');
            me.phone('');
            me.notes('');
            me.active (1);
            me.newsletter(0);
            me.sortkey('');
            me.lastUpdate('');
            me.addressId(null);
            me.addressTypeId(1);
            me.directoryListingTypeId(1);
        }

        private clearValidations() {
            var me = this;
            me.hasErrors(false);
            me.addressNameError('');
        }

        public validate = ():boolean => {
            var me = this;
            me.clearValidations();
            var valid = true;
            var value = me.addressname();
            if (!value) {
                me.addressNameError(": Please enter a name for the address");
                me.hasErrors(true);
                return false;
            }

            return true;
        };


        /**
         * Set fields from address DTO
         */
        public assign = (address : scymAddress) => {
            var me = this;
            me.isAssigned = true;
            me.clearValidations();
            me.addressname(address.addressname);
            me.address1(address.address1);
            me.address2(address.address2);
            me.city(address.city);
            me.state(address.state);
            me.postalcode(address.postalcode);
            me.country(address.country);
            me.phone(address.phone);
            me.notes(address.notes);
            me.active (address.active);
            me.sortkey(address.sortkey);
            me.lastUpdate(address.lastUpdate);
            me.addressId(address.addressId);
            me.newsletter(address.newsletter);
            me.addressTypeId(address.addressTypeId);
            me.directoryListingTypeId(address.directorylistingtypeid);
            var directoryListingItem = me.getDirectoryListingItem();
            me.selectedDirectoryListingType(directoryListingItem);
        };

        public updateScymAddress(address: scymAddress) {
            var me = this;
            address.address1 = me.address1();
            address.address2 = me.address2();
            address.addressname = me.addressname();
            address.city = me.city();
            address.state = me.state();
            address.postalcode = me.postalcode();
            address.country = me.country();
            address.phone = me.phone();
            address.notes = me.notes();
            address.active = me.active();
            address.sortkey = me.sortkey();
            address.newsletter = me.newsletter();
            address.addressTypeId = me.addressTypeId();

            var listingType = me.selectedDirectoryListingType();
            if (listingType) {
                var listingCode = listingType.Value ? Number(listingType.Value) : 0;
                me.directoryListingTypeId(listingCode);
            }
            address.directorylistingtypeid = me.directoryListingTypeId();
        }
    }

    /**
     * View Model class for directory application
     */
    export class ScymDirectoryViewModel implements IMainViewModel {
        static instance: Tops.ScymDirectoryViewModel;
        private application: Tops.IPeanutClient;
        private peanut: Tops.Peanut;

        public family = new clientFamily();

        private insertAssociation = 'none';
        private personUpdateOperation = 'update';

        //  *********** Observables ****************/
        searchValue = ko.observable('');
        searchType = ko.observable('');
        personForm = new personObservable();
        addressForm = new addressObservable();
        personFormHeader : KnockoutComputed<string>; // initialization in constructor
        familiesList = new searchListObservable(6,10);
        personsList =  new searchListObservable(2,12);
        addressesList =  new searchListObservable(2,12);
        addressPersonsList = ko.observableArray<INameValuePair>();
        userCanEdit = ko.observable(true);
        debugMode = ko.observable(false);
        userIsAuthorized = ko.observable(false);
        public showEditButton: KnockoutComputed<boolean>;
        public showAddPersonButton: KnockoutComputed<boolean>;
        public showPersonViewButtons : KnockoutComputed<boolean>;

        // Constructor
        constructor() {
            var me = this;
            Tops.ScymDirectoryViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;

            me.personFormHeader = ko.computed(me.computePersonFormHeader);
            me.showEditButton = ko.computed(me.computeShowEditButton);
            me.showAddPersonButton = ko.computed(me.computeShowAddPersonButton);
            me.showPersonViewButtons = ko.computed(me.computeShowPersonViewButtons);
        }

        private computeShowPersonViewButtons = () => {
            var me = this;
            return (me.userCanEdit() && (me.addressForm.viewState() == 'view' || me.addressForm.viewState() == 'empty'));
        };

        private computeShowAddPersonButton = () => {
            var me = this;
            return (me.family.personCount() < 2 &&
                (me.userCanEdit() && (me.personForm.viewState() == 'view' || me.personForm.viewState() == 'empty')));
        };

        private computeShowEditButton = () => {
            var me = this;
            // userCanEdit() && (personForm.viewState() == 'view' || personForm.viewState() == 'empty'
            return me.userCanEdit() && (me.personForm.viewState() == 'view' || me.personForm.viewState() == 'empty');
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

            // initialize date popups
            jQuery(function() {
                jQuery( ".datepicker" ).datepicker();
            });

            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.
                    me.application.showWaiter('Initializing. Please wait...');
                    me.getInitializations(successFunction);
                }
            );
        }

        getInitializations(doneFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();

            var personId = me.peanut.getRequestParam('pid');



            me.peanut.executeService('directory.InitDirectoryApp',personId, me.handleInitializationResponse)
                .always(function () {
                    me.application.hideWaiter();
                    if (doneFunction) {
                        doneFunction();
                        jQuery('#scym-directory').show();
                    }
                });
        }

        handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                var response = <IInitDirectoryResponse>serviceResponse.Value;
                me.userCanEdit(response.canEdit);
                me.personForm.affiliations(response.affiliationCodes);
                me.personForm.directoryListingTypes(response.directoryListingTypes);
                // me.personForm.membershipTypes(response.membershipTypes);
                me.addressForm.directoryListingTypes(response.directoryListingTypes);
                me.userIsAuthorized(true);
                if (response.family) {
                    me.searchType('Persons');
                    me.selectFamily(response.family);
                }
            }
            else {
                me.userCanEdit(false);
            }
        };

        // ******** Computed observable functions

        /**
         * Compute person form header from selected person.
         * @returns {string}
         */
        public computePersonFormHeader = () => {
            var me = this;
            var name = me.personForm.fullName();
            return name ? name : 'Person';
        };

        // *************** Events ***********

        /**
         * On click find Persons button
         */
        public findFamiliesByPersonName() {
            var me = this;
            me.findFamilies('Persons');
        }

        /**
         * On click find address button
         */
        public findFamiliesByAddressName() {
            var me = this;
            me.findFamilies('Addresses');
        }

        private findFamilies(searchType: string) {
            var me = this;
            me.searchType(searchType);
            me.family.visible(false);
            var request = new KeyValueDTO();
            request.Name = searchType;
            request.Value = me.familiesList.searchValue();

            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');
            me.peanut.executeService('directory.DirectorySearch',request, me.handleFindFamiliesResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private handleFindFamiliesResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.familiesList.setList(list);
                me.familiesList.searchValue('');
            }
        };

        /**
         * on click of add person button in address form
         */
        public findPersonForAddress() {
            var me = this;
            me.personsList.reset();
            me.personForm.search();
        }


        /**
         * on click of create person button dropdown on address form
         */
        public createPersonForAddress() {
            var me = this;
            me.personForm.clear();
            var addressId = me.family.address ? me.family.address.addressId : null;
            me.personForm.edit(addressId);
        }

        /**
         * on click of cancel button, person panel search view
         */
        public cancelPersonSearch() {
            var me = this;
            me.personsList.reset();
            me.personForm.view();
        }

        /**
         * on click of search button, person panel search view
         */
        public findPersons() {
            var me = this;

            var request = new KeyValueDTO();
            request.Name = 'Persons';
            request.Value = me.personsList.searchValue();

            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');
            me.peanut.executeService('directory.DirectorySearch',request, me.showPersonSearchResults)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        /**
         * service response handler for findPersons
         * @param serviceResponse
         */
        public showPersonSearchResults = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.personsList.setList(list);
            }
        };

        /**
         * On click find address button
         */
        public findAddresses() {
            var me = this;
            var request = new KeyValueDTO();
            request.Name = 'Addresses';
            request.Value = me.addressesList.searchValue();

            me.application.hideServiceMessages();
            me.application.showWaiter('Searching...');
            me.peanut.executeService('directory.DirectorySearch',request, me.showAddressSearchResults)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private clearAddressSearchList() {
            var me = this;
            var removeItem = new KeyValueDTO();
            removeItem.Name = '(No address)';
            removeItem.Value = null;
            var list = [];
            if (me.family.address) {
                var removeItem = new KeyValueDTO();
                removeItem.Name = '(No address)';
                removeItem.Value = null;
                list.push(removeItem);
            }
            me.addressesList.setList(list);
        }

        /**
         * Service response handler for findAddresses
         * @param serviceResponse
         */
        public showAddressSearchResults = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = [];
                if (me.family.address) {
                    var removeItem = new KeyValueDTO();
                    removeItem.Name = '(No address)';
                    removeItem.Value = null;
                    list.push(removeItem);
                    list = list.concat(<INameValuePair[]>serviceResponse.Value);
                }
                else {
                   list = <INameValuePair[]>serviceResponse.Value;
                }

                me.addressesList.setList(list);
            }
        };

        /**
         * On click of person link in person form search view
         * @param personItem
         */
        public addPersonToAddress = (personItem : INameValuePair) => {
            var me = this;
            if (me.family.address == null) {
                return;
            }

            var request = new addressPersonServiceRequest();
            request.addressId = me.family.address.addressId;
            request.personId = personItem.Value;

            me.application.hideServiceMessages();
            me.application.showWaiter('Updating...');
            me.peanut.executeService('directory.AddPersonToAddress',request, me.handleAddPersonToAddressResponse)
                .always(function() {
                    me.application.hideWaiter();
                });

        };

        private handleAddPersonToAddressResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var person = <scymPerson>serviceResponse.Value;
                me.personsList.reset();
                me.family.addPersonToList(person);
                me.buildPersonSelectList(person);
                me.personForm.assign(person);
                me.personForm.view();
            }
        };

        /**
         * On click of address link in address form search view
         * @param addressItem
         */
        public assignAddressToPerson = (addressItem : INameValuePair) => {
            var me = this;
            var request = new addressPersonServiceRequest();
            request.addressId = addressItem.Value;
            request.personId = me.family.selectedPersonId;

            me.application.hideServiceMessages();
            me.application.showWaiter('Updating...');
            me.peanut.executeService('directory.ChangePersonAddress',request, me.handleChangePersonAddress)
                .always(function() {
                    me.application.hideWaiter();
                });

            me.addressesList.reset();
            me.addressForm.view();
        };

        private handleChangePersonAddress = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var family = <IScymFamily>serviceResponse.Value;
                var currentPersonId = family.selectedPersonId;
                me.addressesList.reset();
                var selected = me.family.setFamily(family);
                me.refreshFamilyForms(selected);
            }
        };

        /**
         * On click of create button in person form, search view
         */
        public createAddressForPerson() {
            var me = this;
            me.addressesList.reset();
            me.addressForm.edit(me.family.selectedPersonId);
        }

        /**
         * On click of cancel button in address form search view
         */
        public cancelAddressSearch() {
            var me = this;
            me.addressesList.reset();
            me.addressForm.view();
        }


        private selectFamily = (family: IScymFamily) => {
            var me = this;
            me.addressPersonsList([]);
            var selected = me.family.setFamily(family);
            me.refreshFamilyForms(selected);
        };

        private handleFamilyResponse = (serviceResponse: IServiceResponse)=> {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var family = <IScymFamily>serviceResponse.Value;
                me.selectFamily(family);
            }
        };

        private handleUpdateFamilyResponse = (serviceResponse: IServiceResponse)=> {
            var me = this;
            me.addressPersonsList([]);
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var family = <IScymFamily>serviceResponse.Value;
                // var currentSelected = me.family.getSelected();
                // var personId = currentSelected ? currentSelected.personId : 0;
                var selected = me.family.setFamily(family);
                me.refreshFamilyForms(selected);
            }
        };

        private refreshFamilyForms(selected : scymPerson) {
            var me = this;
            if (selected) {
                me.personForm.assign(<scymPerson>selected);
                me.personForm.view();
            }
            else {
                me.personForm.empty();
            }

            if (me.family.address) {
                me.addressForm.assign(me.family.address);
                me.buildPersonSelectList(selected);
                me.addressForm.view();
            } else {
                me.addressPersonsList([]);
                me.addressForm.empty();
            }
            me.buildPersonSelectList(selected);
            me.family.visible(true);
        }

        private buildPersonSelectList(selected) {
            var me = this;
            me.family.persons.sort(function(x: scymPerson,y: scymPerson) {
               if (x.personId === y.personId ) {
                   return 0;
               }
                if (x.sortkey > y.sortkey) {
                    return 1;
                }
                return -1;
            });

            var personList = [];
            if (selected) {
                _.each(me.family.persons, function (person:scymPerson) {
                    if (person.editState != editState.deleted && person.personId != selected.personId) {
                        var item = new KeyValueDTO();
                        item.Name = personObservable.makeFullName(person.firstName, person.middleName, person.lastName);
                        item.Value = person.personId.toString();
                        personList.push(item);
                    }
                }, selected);
                if (me.userCanEdit()) {
                    var newPersonItem = new KeyValueDTO();
                    newPersonItem.Value = 'new';
                    newPersonItem.Name = 'Find or create new person';
                    personList.push(newPersonItem);
                }
            }
            me.addressPersonsList(personList);
        }

        /**
         * On click item link in found panel
         * @param item
         */
         public displayFamily = (item : INameValuePair) => {
             var me = this;
             me.family.visible(false);
             me.familiesList.reset();
             me.personForm.clear();
             me.personForm.close();
             me.addressForm.clear();
             me.addressForm.close();
             me.addressPersonsList([]);

            var request = new KeyValueDTO();
            request.Name = me.searchType();
            request.Value = item.Value;

            me.application.hideServiceMessages();
            me.application.showWaiter('Locating family...');
            me.peanut.executeService('directory.GetFamily',request,me.handleFamilyResponse)
                .always(function() {
                    me.application.hideWaiter();
                });

         };



        /**
         * on select person in persons button dropdown on address panel
         * @param item
         */
        public selectPerson = (item : INameValuePair) => {
            var me = this;
            if (item.Value == 'new') {
                me.personForm.search();
            }
            else {
                var selected = me.family.selectPerson(item.Value);
                if (selected) {
                    me.buildPersonSelectList(selected);
                    me.personForm.assign(selected);
                    me.personForm.view();
                }
                else {
                    me.personForm.empty();
                }
            }
        };

        /**
         * on click of edit button in person panel view mode
         */
        public editPerson() {
            var me = this;
            // avoid accidental click as default button.
            var addressFormState = me.addressForm.viewState();
            var personFormState = me.personForm.viewState();
            // if ((addressFormState == 'view' || addressFormState == 'empty') && personFormState == 'view') {
                me.personForm.edit();
            // }
        }

        /**
         * on click of move button in person panel view mode
         */
        public movePerson() {
            var me = this;
            me.addressesList.reset();
            me.clearAddressSearchList();
            me.addressForm.search();
        }

        /**
         * On click of edit button on address panel view mode
         */
        public editAddress() {
            var me = this;
            me.addressForm.edit();
        }

        public cancelAddressEdit() {
            var me = this;
            // rollback changes
            if (me.family.isLoaded()) {
                me.addressForm.assign(me.family.address);
                me.addressForm.view();
            }
            else {
                me.family.visible(false);
                me.addressForm.clear();
            }
        }

        public cancelPersonEdit() {
            var me = this;
            // rollback changes to form
            if (me.family.isLoaded()) {
                var selected = me.family.getSelected();
                me.personForm.assign(selected);
                me.personForm.view();
            }
            else {
                me.family.visible(false);
                me.personForm.clear();
            }
        }

        /**
         * on save button click on person panel in edit mode
         */
        public savePerson(): void {
            var me = this;
            if (!me.personForm.validate()) {
                return;
            }

            var person = null;
            var updateMessage = 'Updating person...';
            var personId = me.personForm.personId();

            if (!personId) {
                person = new scymPerson();
                person.editState = editState.created;
            }
            else {
                person = me.family.getPersonById(personId);
                person.editState = editState.updated;
            }
            me.personForm.updateScymPerson(person);

            if (person.editState == editState.created && me.personForm.relationId) {
                var request = new newPersonForAddressRequest();
                request.person = person;
                request.addressId =  me.family.address ? me.family.address.addressId : null;
                me.application.showWaiter("Adding new person to address ...");
                me.peanut.executeService('directory.NewPersonForAddress',request, me.handleAddPersonToAddressResponse)
                    .always(function() {
                        me.application.hideWaiter();
                    });
            }
            else {
                var updateMessage = person.editState == editState.created ? 'Adding person ...' : 'Updating person...';
                me.application.showWaiter(updateMessage);
                me.peanut.executeService('directory.UpdatePerson',person, me.handleUpdatePersonResponse)
                    .always(function() {
                        me.application.hideWaiter();
                    });
            }
        }

         private handleUpdatePersonResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var person = <scymPerson>serviceResponse.Value;
                me.family.addPersonToList(person);
                me.personForm.assign(person);
                me.family.visible(true);
                me.personForm.view();
                me.addressForm.view();
            }
        };

        /**
         * handle save click on address form in edit mode
         */
        public saveAddress() {
            var me = this;

            if (!me.addressForm.validate()) {
                return;
            }


            var address = null;
            var addressId = me.addressForm.addressId();

            if (!addressId) {
                address = new scymAddress();
                address.editState = editState.created;
            }
            else {
                address = me.family.address;
                address.editState = editState.updated;
            }
            me.addressForm.updateScymAddress(address);

            if (address.editState == editState.created && me.addressForm.relationId) {
                var request = new newAddressForPersonRequest();
                request.address = address;
                request.personId = me.family.selectedPersonId;
                me.application.showWaiter("Adding new address for person ...");
                me.peanut.executeService('directory.NewAddressForPerson',request, me.handleChangePersonAddress)
                    .always(function() {
                        me.application.hideWaiter();
                    });
            }
            else {
                var updateMessage = address.editState == editState.created ? 'Adding address ...' : 'Updating address...';
                me.application.showWaiter(updateMessage);
                me.peanut.executeService('directory.UpdateAddress',address, me.handleUpdateAddressResponse)
                    .always(function() {
                        me.application.hideWaiter();
                    });
            }
        }

        private handleUpdateAddressResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var address = <scymAddress>serviceResponse.Value;
                var selected = me.family.setAddress(address);
                me.addressForm.assign(address);
                me.personForm.view();
                me.addressForm.view();
            }
        };


        /**
         *  On select new person upper panel
         */
        public newPerson = () => {
            var me=this;
            me.familiesList.reset();
            me.personForm.clear();
            me.addressForm.clear();
            me.family.empty();
            me.personForm.edit();
            me.addressForm.empty();
            me.family.visible(true);
        };

        public newAddress = () => {
            var me=this;
            me.familiesList.reset();
            me.personForm.clear();
            me.addressForm.clear();
            me.family.empty();
            me.addressForm.edit();
            me.personForm.empty();
            me.family.visible(true);
        };

        private createSelectedPersonRequest() {
            var me = this;
            var request = new addressPersonServiceRequest();
            request.personId = me.family.selectedPersonId;
            request.addressId = me.family.address ?  me.family.address.addressId : 0;
            return request;
        }

        public deletePerson() {
            var me = this;
            me.showPersonDeleteConfirmForm();
        }

        hidePersonDeleteConfirmForm() {
            jQuery("#confirm-delete-person-modal").modal('hide');
        }

        showPersonDeleteConfirmForm() {
            var me = this;
            jQuery("#confirm-delete-person-modal").modal('show');
        }


        hideAddressDeleteConfirmForm() {
            jQuery("#confirm-delete-address-modal").modal('hide');
        }

        showAddressDeleteConfirmForm() {
            var me = this;
            jQuery("#confirm-delete-address-modal").modal('show');
        }


        public executeDeletePerson() {
            var me = this;
            me.hidePersonDeleteConfirmForm();

            var request = me.family.selectedPersonId;

            me.application.hideServiceMessages();
            me.application.showWaiter('Delete person...');
            me.peanut.executeService('directory.DeletePerson',request, me.handleRemovePersonResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private handleRemovePersonResponse = (serviceResponse: IServiceResponse) => {
            // TODO: ui issue, last person disappears if address deleted.
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var selected = me.family.removePerson(me.family.selectedPersonId);
                me.buildPersonSelectList(selected);
                if (selected) {
                    me.personForm.assign(selected);
                    me.personForm.view();
                }
                else {
                    me.personForm.empty();
                }
            }
        };

        public deleteAddress() {
            var me = this;
            me.showAddressDeleteConfirmForm();
        }

        public executeDeleteAddress() {
            var me = this;
            me.hideAddressDeleteConfirmForm();
            var addressId = me.family.address ? me.family.address.addressId : 0;
            if (!addressId) {
                return;
            }

            var request = addressId;

            me.application.hideServiceMessages();
            me.application.showWaiter('Deleting address...');
            me.peanut.executeService('directory.DeleteAddress',request, me.handleClearAddressResponse)
                .always(function() {
                    me.application.hideWaiter();
                });

        }

        private handleClearAddressResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.family.clearAddress();
                me.addressForm.empty();
                me.personForm.view();
            }
        };

    }
}

Tops.ScymDirectoryViewModel.instance = new Tops.ScymDirectoryViewModel();
(<any>window).ViewModel = Tops.ScymDirectoryViewModel.instance;