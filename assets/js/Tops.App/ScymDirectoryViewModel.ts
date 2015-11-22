/**
 * Created by Terry on 7/13/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

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
        public personId : number = 0; // database entity id
        public firstName='';
        public middleName='';
        public lastName='';
        public username : string = '';
        public address : string = '';
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

        public id : string = ''; // client side id
        public editstate : number = editState.created;
    }

    /**
     * address DTO as returned from services
     */
    export class scymAddress {
        public addressid : number = 0; // database entity id
        public addresstype: number = 1;
        public addressname = '';
        public address1 = '';
        public address2 = '';
        public city = '';
        public state = '';
        public postalcode = '';
        public country = '';
        public phone = '';
        public notes = '';
        public newsletter  = '';
        public active : number = 1;
        public sortkey = '';

        public id : string = ''; // client side id
        public editstate: number = editState.created;
    }

    /**
     * Related persons and address DTO as returned by service
     */
    export class scymFamily {
        public address : scymAddress;
        public persons: scymPerson[] = [];

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
                return person.editstate != editState.deleted;
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
                    person.editstate = editState.unchanged;
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
        public addPerson = (person : personObservable) => {
            var me = this;
            var newPerson = new scymPerson();
            newPerson.editstate = 1;
            newPerson.personId = 0;
            me.newId = me.newId + 1;
            newPerson.id = 'new:' + me.newId.toString();

            newPerson.firstName = person.firstName();
            newPerson.middleName = person.middleName();
            newPerson.lastName = person.lastName();
            newPerson.username = person.username();
            newPerson.phone = person.phone();
            newPerson.phone2 = person.phone2();
            newPerson.email = person.email();
            newPerson.newsletter = person.newsletter();
            newPerson.dateOfBirth = person.dateOfBirth();
            newPerson.notes = person.notes();
            newPerson.junior = person.junior();
            newPerson.active = person.active();
            newPerson.sortkey = person.sortkey();
            newPerson.affiliationcode = person.affiliationcode();
            newPerson.otheraffiliation = person.otheraffiliation();
            newPerson.directorylistingtypeid = person.directorylistingtypeid();
            newPerson.editstate = editState.created;

            me.persons.push(newPerson);
            me.changeCount = me.changeCount + 1;
            me.personCount(me.persons.length);

        };

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
            person.editstate = editState.deleted;
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

        /**
         * reset fields
         */
        public clear() {
            var me=this;
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
        }

        /**
         * set fields from person DTO
         */
        public assign = (person: scymPerson) => {
            var me=this;
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
        };

        public id = '';
        public firstName = ko.observable('');
        public middleName = ko.observable('');
        public lastName = ko.observable('');
        public username = ko.observable('');
        public address = ko.observable('');
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
        public addressName = ko.observable('');
        public addresstype = ko.observable(1);
        public addressname= ko.observable('');
        public address1= ko.observable('');
        public address2= ko.observable('');
        public city= ko.observable('');
        public state= ko.observable('');
        public postalcode= ko.observable('');
        public country= ko.observable('');
        public phone= ko.observable('');
        public notes= ko.observable('');
        public newsletter = ko.observable('');
        public active  = ko.observable(1);
        public sortkey= ko.observable('');

        /**
         * reset fields
         */
        public clear() {
            var me = this;
            me.id = '';
            me.addressName('');
            me.addresstype(1);
            me.addressname('');
            me.address1('');
            me.address2('');
            me.city('');
            me.state('');
            me.postalcode('');
            me.country('');
            me.phone('');
            me.notes('');
            me.newsletter('');
            me.active (1);
            me.sortkey('');
        }

        /**
         * Set fields from address DTO
         */
        public assign = (address : scymAddress) => {
            var me = this;
            me.addressName(address.addressname);
            me.addresstype(address.addresstype);
            me.addressname(address.addressname);
            me.address1(address.address1);
            me.address2(address.address2);
            me.city(address.city);
            me.state(address.state);
            me.postalcode(address.postalcode);
            me.country(address.country);
            me.phone(address.phone);
            me.notes(address.notes);
            me.newsletter(address.newsletter);
            me.active (address.active);
            me.sortkey(address.sortkey);

        };
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
            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.
                    // todo: get user permission from service
                    me.userCanEdit(true);

                    if (successFunction) {
                        successFunction();
                    }
                }
            );
        }

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
            me.family.visible(false);
            me.familiesList.searchValue('');

            // todo: get search result from service
            var list = me.makeFakePersons();
            var response = new fakeServiceResponse(list);
            me.handleFindFamiliesResponse(response);

            me.searchType('Persons');
        }

        /**
         * On click find address button
         */
        public findFamiliesByAddressName() {
            var me = this;
            me.family.visible(false);
            me.familiesList.searchValue('');
            me.searchType('Addresses');

            //todo: get family search from service
            var list = me.makeFakeAddresses();
            var response = new fakeServiceResponse(list);
            me.handleFindFamiliesResponse(response);
        }

        private handleFindFamiliesResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <INameValuePair[]>serviceResponse.Value;
                me.familiesList.setList(list);
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

            var selected = me.family.getSelected();
            me.buildPersonSelectList(selected);
            me.personForm.view();
        };

        /**
         * On click of address link in address form search view
         * @param addressItem
         */
        public assignAddressToPerson = (addressItem : INameValuePair) => {
            var me = this;
            // todo: handle address/person link up

            me.addressesList.reset();
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
                    if (person.editstate != editState.deleted && person.personId != selected.personId) {
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

            // todo: get family data from service
            var family = me.makeFakeFamily(item.Name);
            var response = new fakeServiceResponse(family);

            me.setupFamily(response);
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
            me.addressForm.view();
        }

        public cancelPersonEdit() {
            var me = this;
            me.personForm.view();
        }

        /**
         * on save button click on person panel in edit mode
         */
        public savePerson() {
            var me = this;
            // todo: implement data persistance for person
            me.personForm.view();
        }

        /**
         * handle save click on address form in edit mode
         */
        public saveAddress() {
            var me = this;
            //todo: implement data persistance for address
            me.addressForm.view();
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
            alert('New address');
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
            family.address.addressid = 1;
            family.address = new scymAddress();
            family.address.addressname = 'Terry SoRelle and Liz Yeats';
            family.address.address1 = '904 E. Meadowmere';
            family.address.city = 'Austin';
            family.address.state = 'TX';
            family.address.postalcode = '78758';

            var terry = new scymPerson();
            terry.personId = 1;
            terry.firstName = 'Terry';
            terry.lastName = 'SoRelle';
            terry.email = 'tls@2quakers.net';

            var liz = new scymPerson();
            liz.personId = 2;
            liz.firstName = 'Elizabeth';
            liz.lastName = 'Yeats';
            liz.email = 'liz.yeats@outlook.com';

            family.persons[0] = liz;
            family.persons[1] = terry;

            return family;
        }
    }
}

Tops.ScymDirectoryViewModel.instance = new Tops.ScymDirectoryViewModel();
(<any>window).ViewModel = Tops.ScymDirectoryViewModel.instance;