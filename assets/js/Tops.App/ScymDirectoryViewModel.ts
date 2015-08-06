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

module Tops {

    /**
     * Constants for scym entities editState
     */
    export class editState {
        public static unchanged : number = 0;
        public static created : number = 1;
        public static updated : number = 2;
        public static deleted : number = 3;
    }

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
        public otheraffiliation : string = '';
        public directorylistingtypeid: number=1;
        public lastUpdate : string = '';
        public organization: string = '';

        public id : string = ''; // client side id
        public editState : number = editState.created;
    }

    /**
     * address DTO as returned from services
     */
    export class scymAddress {
        public addressId : any = null; // database entity id
        public addressname = '';
        public address1 = '';
        public address2 = '';
        public city = '';
        public state = '';
        public postalcode = '';
        public country = '';
        public phone = '';
        public notes = '';
        public active : number = 1;
        public sortkey = '';
        public lastUpdate : string = '';

        public id : string = ''; // client side id
        public editState: number = editState.created;
    }

    /**
     * Related persons and address DTO as returned by service
     */
    export class scymFamily {
        public address : scymAddress;
        public persons: scymPerson[] = [];

    }

    export interface IInitDirectoryResponse {
        canEdit : boolean;
        directoryListingTypes : INameValuePair[];
        affiliationCodes : INameValuePair[];
    }


    /**
     * Local structure to track related persons and addresses and observables
     */
    class clientFamily {
        public address : scymAddress;
        public persons: scymPerson[] = [];
        public selectedPersonId : string = '';
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
        public setFamily(family: scymFamily){
            var me = this;
            me.setAddress(family.address);
            var selected = me.setPersons(family.persons);

            return selected;
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
        public clearAddress() {
            var me = this;
            me.address = null;
            me.hasAddress(false);
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
                me.selectedPersonId = firstPerson.id;
            }
            return <scymPerson>firstPerson;
        }



        /**
         * Assign persons list
         * @param persons
         * @returns {scymPerson}
         */
        public setPersons(persons: scymPerson[]) : scymPerson {
            var me = this;
            me.selectedPersonId = '';
            me.personCount(0);

            var firstPerson : scymPerson = null;
            if (persons) {
                _.each(persons,function(person: scymPerson){
                    person.id = person.personId.toString();
                    person.editState = editState.unchanged;
                });
                me.persons = persons;
                firstPerson = me.selectFirstPerson();
            }
            else {
                me.persons = [];
            }

            me.personCount(persons.length);
            return firstPerson;
        }

        /**
         * Add new person to list from person observable
         * @param person
         */
        public addPerson = (newPerson : scymPerson) => {
            var me = this;
            newPerson.editState = editState.created;
            newPerson.personId = 0;
            me.newId = me.newId + 1;
            newPerson.id = 'new:' + me.newId.toString();
            me.persons.push(newPerson);
            me.personCount(me.persons.length);
        };

        public addPersonToList(person: scymPerson) {
            var me = this;
            me.persons.push(person);
            me.personCount(me.persons.length);
        }

        /**
         * Return selected person object
         * @returns {scymPerson}
         */
        public getSelected() : scymPerson {
            var me = this;

            var selected = _.find(me.persons,function(person) {
                return me.selectedPersonId == person.id;
            },me);
            return <scymPerson>selected;
        }

        public getPersonById(id : string) : scymPerson {
            var me = this;

            var result = _.find(me.persons,function(person) {
                return id == person.id;
            },me);
            return <scymPerson>result;

        }

        /**
         * Set selected person by id
         * @param id
         * @returns {scymPerson}
         */
        public selectPerson(id : string) : scymPerson {
            var me = this;
            var selected = null;
            if (id) {
                selected = _.find(me.persons,function(person) {
                    return person.id == id;
                },me);
                if (selected) {
                    me.selectedPersonId = id;
                }
            }
            else {
                me.selectedPersonId = '';
            }
            return <scymPerson>selected;
        }

        /**
         * Set deleted flag on person for id
         * @param id
         * @returns {scymPerson} (person deleted)
         */
        public removePerson(id : string) {
            //todo: call this when delete is implemented
            var me = this;
            var person = _.find(me.persons, function(person: scymPerson){
                return person.id == id;
            });
            person.editState = editState.deleted;
            me.selectFirstPerson();
            return <scymPerson>person;
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
        }

        /**
         *  Assign result list
         */
        public setList(list : Tops.INameValuePair[]) {
            var me = this;
            me.itemList = list;
            var itemCount = list.length;
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

    /**
     * base class for person panel and address panel
     */
    export class editPanel {

        public searchList = new searchListObservable(2,10);
        public viewState = ko.observable('');
        public hasErrors = ko.observable(false);
        /**
         * set view state 'edit'
         */
        public edit(){
            var me = this;
            me.viewState('edit');
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
            me.viewState('view');
        }
    }

    /**
     * observable container for person panel
     */
    export class personObservable extends editPanel{

        public id = '';
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
        public otheraffiliation = ko.observable('');
        public directorylistingtypeid= ko.observable(1);
        public lastUpdate = ko.observable('');
        public organization = ko.observable('');

        public selectedAffiliation : KnockoutObservable<INameValuePair> = ko.observable(null);
        public selectedDirectoryListingType : KnockoutObservable<INameValuePair> = ko.observable(null);
        public affiliations = ko.observableArray<INameValuePair>([]);
        public directoryListingTypes = ko.observableArray<INameValuePair>([]);

        public affiliation : KnockoutComputed<string>;
        public hasAffiliation : KnockoutComputed<boolean>;
        public emailLink : KnockoutComputed<string>;
        // public directoryListing: KnockoutComputed<string>;

        public firstNameError = ko.observable('');
        public lastNameError = ko.observable('');
        public emailError = ko.observable('');

        constructor() {
            super();
            var me = this;
            // me.directoryListing = ko.computed(me.computeDirectoryListing);
            me.affiliation = ko.computed(me.computeAffiliation);
            me.hasAffiliation = ko.computed(me.computeHasAffiliation);
            me.emailLink = ko.computed(me.computeEmailLink);
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

        private getAffiliationItem = () => {
            var me = this;
            var lookup = me.affiliations();
            var key = me.affiliationcode();
            var result = _.find(lookup,function(item : INameValuePair) {
                return item.Value == key;
            },me);
            return result;
        };

        private getDirectoryListingItem = () => {
            var me = this;
            var lookup = me.directoryListingTypes();
            var id = me.directorylistingtypeid();
            if (!id) {
                id = 0;
            }
            var key = id.toString()

            var result = _.find(lookup,function(item : INameValuePair) {
                return item.Value == key;
            },me);
            return result;
        };

        computeAffiliation = () => {
            var me = this;
            var result = me.getAffiliationItem();
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
            me.clearValidations();
            me.firstName('');
            me.middleName('');
            me.lastName('');
            me.id = '';
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
            me.otheraffiliation('');
            me.directorylistingtypeid= ko.observable(1);
            me.lastUpdate('');
            me.personId('');
            me.organization('');
            me.selectedAffiliation(null);
        }

        public clearValidations() {
            var me = this;
            me.firstNameError('');
            me.lastNameError('');
            me.emailError('');
            me.hasErrors(false);
        }

        /**
         * set fields from person DTO
         */
        public assign = (person: scymPerson) => {
            var me=this;
            me.clearValidations();
            me.firstName(person.firstName);
            me.middleName(person.middleName);
            me.lastName(person.lastName);
            me.id = person.id;
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
            me.otheraffiliation(person.otheraffiliation);
            me.directorylistingtypeid(person.directorylistingtypeid);
            me.lastUpdate(person.lastUpdate);
            me.personId(person.personId);
            me.organization(person.organization);
            var affiliationItem = me.getAffiliationItem();
            me.selectedAffiliation(affiliationItem);
            var directoryListingItem = me.getDirectoryListingItem();
            me.selectedDirectoryListingType(directoryListingItem);
        };

        public updateScymPerson = (person: scymPerson) => {
            var me = this;

            person.active = me.active();
            var affiliation = me.selectedAffiliation();
            if (affiliation) {
                me.affiliationcode(affiliation.Value);
            }
            person.affiliationcode = me.affiliationcode();

            var listingType = me.selectedDirectoryListingType();
            if (listingType) {
                var listingCode = listingType.Value ? Number(listingType.Value) : 0;
                me.directorylistingtypeid(listingCode);
            }
            person.directorylistingtypeid = me.directorylistingtypeid();

            person.dateOfBirth = me.dateOfBirth();
            person.email = me.email();
            person.firstName = me.firstName();
            person.lastName = me.lastName();
            person.junior = me.junior();
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
            return personObservable.makeFullName(first,last,middle);
        }


    }

    /**
     * observable container for address panel
     */
    export class addressObservable extends editPanel {
        public id = '';
        public addressId : KnockoutObservable<any> =  ko.observable();
        public addressname= ko.observable('');
        public address1= ko.observable('');
        public address2= ko.observable('');
        public city= ko.observable('');
        public state= ko.observable('');
        public postalcode= ko.observable('');
        public country= ko.observable('');
        public phone= ko.observable('');
        public notes= ko.observable('');
        public active  = ko.observable(1);
        public sortkey= ko.observable('');
        public lastUpdate = ko.observable('');
        public cityLocation : KnockoutComputed<string>;

        public addressNameError = ko.observable('');

        constructor() {
            super();
            var me = this;
            me.cityLocation = ko.computed(me.computeCityLocation);
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
            me.clearValidations();
            me.id = '';
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
            me.sortkey('');
            me.lastUpdate('');
            me.addressId(null);
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

        //  *********** Observables ****************/
        searchValue = ko.observable('');
        searchType = ko.observable('');
        personForm = new personObservable();
        addressForm = new addressObservable();
        personFormHeader : KnockoutComputed<string>; // initialization in constructor
        familiesList = new searchListObservable(6,3);
        personsList =  new searchListObservable(3,6);
        addressesList =  new searchListObservable(3,6);
        addressPersonsList = ko.observableArray<INameValuePair>();
        userCanEdit = ko.observable(true);
        userIsAuthorized = ko.observable(false);

        // Constructor
        constructor() {
            var me = this;
            Tops.ScymDirectoryViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;

            me.personFormHeader = ko.computed(me.computePersonFormHeader);
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
            me.peanut.executeService('directory.InitDirectoryApp','', me.handleInitializationResponse)
                .always(function() {
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
                me.userIsAuthorized(true);
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
            me.personForm.edit();
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
            me.personsList.searchValue('');

            // todo: get persons list from service
            var list = me.makeFakePersons();
            var response = new fakeServiceResponse(list);

            me.showPersonSearchResults(response);
        }

        /**
         * service response handler for findPersons
         * @param serviceResponse
         */
        public showPersonSearchResults(serviceResponse: IServiceResponse) {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.personsList.setList(list);
            }
        }

        /**
         * On click find address button
         */
        public findAddresses() {
            var me = this;

            // todo: get address list from service
            var list = me.makeFakeAddresses();
            var response = new fakeServiceResponse(list);
            me.showAddressSearchResults(response);

        }

        /**
         * Service response handler for findAddresses
         * @param serviceResponse
         */
        public showAddressSearchResults(serviceResponse: IServiceResponse) {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.addressesList.setList(list);
            }
        }


        /**
         * On click of person link in person form search view
         * @param personItem
         */
        public addPersonToAddress = (personItem : INameValuePair) => {
            var me = this;
            me.personsList.reset();


            // todo: handle person/address link up
            var testPerson = new scymPerson();
            testPerson.id = personItem.Value;
            testPerson.firstName = 'Test';
            testPerson.lastName = 'Person';
            var serviceResponse = new fakeServiceResponse(testPerson);
            // todo call getPerson service with handleGetPersonResponse
            me.handleGetPersonResponse(serviceResponse);


        };
        private handleGetPersonResponse(serviceResponse: IServiceResponse) {
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var person = <scymPerson>serviceResponse.Value;
                person.id = person.personId.toString();
                person.addressId = this.family.address ? this.family.address.addressId : null;
                this.personForm.assign(person);
                this.personForm.edit();
            }
        }
        /**
         * On click of address link in address form search view
         * @param addressItem
         */
        public assignAddressToPerson = (addressItem : INameValuePair) => {
            var me = this;
            me.addressesList.reset();

            // todo: handle address/person link up





            me.addressForm.view();
        };



        /**
         * On click of create button in person form, search view
         */
        public createAddressForPerson() {
            var me = this;
            // todo: handle new address creation

            me.addressesList.reset();
            me.addressForm.edit();
        }

        /**
         * On click of cancel button in address form search view
         */
        public cancelAddressSearch() {
            var me = this;
            me.addressesList.reset();
            me.addressForm.view();
        }

        private setupFamily = (serviceResponse: IServiceResponse)=> {
            var me = this;
            me.addressPersonsList([]);
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var family = <scymFamily>serviceResponse.Value;
                var selected = me.family.setFamily(family);

                if (selected) {
                    me.personForm.assign(<scymPerson>selected);
                    me.personForm.view();
                }
                else {
                    me.personForm.empty();
                }

                if (family.address) {
                    me.addressForm.assign(family.address);
                    this.buildPersonSelectList(selected);
                    me.addressForm.view();
                } else {
                    me.addressPersonsList([]);
                    me.addressForm.empty();
                }
                me.buildPersonSelectList(selected);
                me.family.visible(true);
            }
        };

        private buildPersonSelectList(selected) {
            var me = this;
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
            me.peanut.executeService('directory.GetFamily',request,me.setupFamily)
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

        public selectAddress = (item : INameValuePair) => {
            var me = this;

        };

        /**
         * on click of edit button in person panel view mode
         */
        public editPerson() {
            var me = this;
            me.personForm.edit();
        }

        /**
         * on click of move button in person panel view mode
         */
        public movePerson() {
            var me = this;
            me.addressesList.reset();

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
            me.addressForm.assign(me.family.address);
            me.addressForm.view();
        }

        public cancelPersonEdit() {
            var me = this;
            // rollback changes to form
            var selected = me.family.getSelected();
            me.personForm.assign(selected);
            me.personForm.view();
        }

        /**
         * on save button click on person panel in edit mode
         */
        public savePerson(): void {
            var me = this;
            var person = null;
            if (!me.personForm.validate()) {
                return;
            }

            if (!me.personForm.id) {
                person = new scymPerson();
                me.personForm.updateScymPerson(person);
                me.family.addPerson(person);
            }
            else {
                person = me.family.getPersonById(me.personForm.id);
                if (person == null){
                    person = new scymPerson();
                    person.address = me.family.address.addressId;
                    me.personForm.updateScymPerson(person);
                    me.family.addPersonToList(person);
                    me.family.selectedPersonId = person.id;
                }
                else {
                    me.personForm.updateScymPerson(person);
                }
                person.editState = editState.updated;
            }
            me.saveChanges();
        }

        private saveChanges() {
            var me = this;

            var changes = new scymFamily();
            if (me.family.address.editState != editState.unchanged) {
                changes.address = me.family.address;
            }

            var familyPersons = me.family.persons;
            changes.persons = _.filter(me.family.persons, function(person: scymPerson) {
                return person.editState != editState.unchanged;
            },familyPersons);

            if (changes.address != null || changes.persons.length > 0) {
                // todo: implement data persistance for family, replace with service call 'changes' as parameter.
                var response = new fakeServiceResponse(null);
                me.handleUpdateResponse(response);
            }
        }
        private handleUpdateResponse(serviceResponse: IServiceResponse) {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.personForm.view();
                me.addressForm.view();
            }
        }

        /**
         * handle save click on address form in edit mode
         */
        public saveAddress() {
            var me = this;
            if (me.addressForm.validate()) {
                if (me.family.address == null) {
                    me.family.address = new scymAddress();
                    me.family.address.editState = editState.created;
                }
                else {
                    me.family.address.editState = editState.updated;
                }
                me.addressForm.updateScymAddress(me.family.address);
                me.saveChanges();
            }
        }


        /**
         *  On select new person
         */
        public newPerson = () => {
            var me=this;
            me.familiesList.reset();
            me.personForm.clear();
            me.family.visible(true);
            me.personForm.edit();
            me.addressForm.empty();
            // todo: handle new person persistence
        };

        public newAddress() {
            var me=this;
            me.familiesList.reset();
            me.personForm.clear();
            me.addressForm.clear();
            me.family.visible(true);
            me.addressForm.edit();
            me.personForm.empty();
            // todo: handle address persistence
        }

        // ******* FAKES **********
        private makeFakePersons() {
            var me = this;
            var list = [];
            list.push(me.makeFakeListItem(1,"Terry SoRelle"));
            list.push(me.makeFakeListItem(2,"Elizabeth Yeats"));
            list.push(me.makeFakeListItem(3,"Bill Wilkinson"));
            list.push(me.makeFakeListItem(4,"Shelly Angel"));
            list.push(me.makeFakeListItem(5,"Terry SoRelle"));
            list.push(me.makeFakeListItem(6,"Jonathan Polacheck"));
            list.push(me.makeFakeListItem(7,"Bill Wilkinson"));
            list.push(me.makeFakeListItem(8,"Terry SoRelle"));
            list.push(me.makeFakeListItem(9,"Elizabeth Yeats"));
            list.push(me.makeFakeListItem(10,"Bill Wilkinson"));
            list.push(me.makeFakeListItem(11,"Shelly Angel"));
            list.push(me.makeFakeListItem(12,"Jonathan Polacheck"));
            list.push(me.makeFakeListItem(13,"Terry SoRelle"));
            list.push(me.makeFakeListItem(14,"Elizabeth Yeats"));
            list.push(me.makeFakeListItem(15,"Bill Wilkinson"));
            list.push(me.makeFakeListItem(16,"Shelly Angel"));
            list.push(me.makeFakeListItem(17,"Jonathan Polacheck"));
            list.push(me.makeFakeListItem(18,"Walter Polacheck"));
            list.push(me.makeFakeListItem(19,"Homer Polacheck"));
            return list;
        }

        private makeFakeAddresses() {
            var me = this;
            var list = [];
            list.push(me.makeFakeListItem(1,"Terry SoRelle and Liz Yeats"));
            list.push(me.makeFakeListItem(4,"Bill and Denise Wilkinson"));
            list.push(me.makeFakeListItem(6,"Shelly Angel"));
            list.push(me.makeFakeListItem(7,"Jonathan Polacheck and Kirsten Dean Polacheck"));
            list.push(me.makeFakeListItem(11,"Terry SoRelle and Liz Yeats"));
            list.push(me.makeFakeListItem(14,"Bill and Denise Wilkinson"));
            list.push(me.makeFakeListItem(16,"Shelly Angel"));
            list.push(me.makeFakeListItem(17,"Jonathan Polacheck and Kirsten Dean Polacheck"));
            list.push(me.makeFakeListItem(21,"Terry SoRelle and Liz Yeats"));
            list.push(me.makeFakeListItem(24,"Bill and Denise Wilkinson"));
            list.push(me.makeFakeListItem(26,"Shelly Angel"));
            list.push(me.makeFakeListItem(27,"Jonathan Polacheck and Kirsten Dean Polacheck"));
            list.push(me.makeFakeListItem(31,"Terry SoRelle and Liz Yeats"));
            list.push(me.makeFakeListItem(34,"Bill and Denise Wilkinson"));
            list.push(me.makeFakeListItem(36,"Shelly Angel"));
            list.push(me.makeFakeListItem(37,"Jonathan Polacheck and Kirsten Dean Polacheck"));
            list.push(me.makeFakeListItem(41,"Terry SoRelle and Liz Yeats"));
            list.push(me.makeFakeListItem(44,"Bill and Denise Wilkinson"));
            list.push(me.makeFakeListItem(46,"Shelly Angel"));
            list.push(me.makeFakeListItem(47,"Jonathan Polacheck and Kirsten Dean Polacheck"));
            return list;
        }
        private makeFakeListItem(id: number, text: string) {
            var result = new Tops.KeyValueDTO();
            result.Name = text;
            result.Value = id.toString();
            return result;
        }

        private makeFakeFamily(name: string) {
            var family = new scymFamily();
            family.address = new scymAddress();
            //if (name == 'Terry SoRelle' || name == 'Liz Yeats' || name == 'Terry SoRelle and Liz Yeats') {
            family.address.addressId = 1;
            family.address = new scymAddress();
            family.address.addressname = 'Terry SoRelle and Liz Yeats';
            family.address.address1 = '904 E. Meadowmere';
            family.address.city = 'Austin';
            family.address.state = 'TX';
            family.address.postalcode = '78758';
            family.address.editState = editState.unchanged;

            var terry = new scymPerson();
            terry.personId = 1;
            terry.firstName = 'Terry';
            terry.lastName = 'SoRelle';
            terry.email = 'tls@2quakers.net';
            terry.editState = editState.unchanged;
            terry.affiliationcode = 'AU TX';
            terry.directorylistingtypeid = 2;
            terry.newsletter = 1;
            terry.junior = 0;
            terry.notes = 'Some notes for terry.';

            var liz = new scymPerson();
            liz.personId = 2;
            liz.firstName = 'Elizabeth';
            liz.lastName = 'Yeats';
            liz.email = 'liz.yeats@outlook.com';
            liz.editState = editState.unchanged;
            liz.affiliationcode = 'NONE';
            liz.directorylistingtypeid = 1;
            liz.newsletter = 0;
            liz.junior = 1;
            liz.organization = 'Friends General Conference';
            liz.otheraffiliation = 'Friendship Meeting, Greensboro, NC';
            liz.notes = 'Some notes here';

            family.persons[0] = liz;
            family.persons[1] = terry;

            return family;
        }
    }


}

Tops.ScymDirectoryViewModel.instance = new Tops.ScymDirectoryViewModel();
(<any>window).ViewModel = Tops.ScymDirectoryViewModel.instance;