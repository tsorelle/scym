/// <reference path="./user.d.ts" />
/// <reference path="../Tops.Peanut/peanut.d.ts"/>
declare module Tops {
    export interface IRegistrationInfo {
        registrationId : any;
        active : number;
        year : string;
        registrationCode : string;
        statusId : number; // lookup: registration statustypes
        name : string;
        address : string;
        city : string;
        phone : string;
        email : string;
        receivedDate : any;
        notes : string;
        scymNotes : string;
        statusDate : any;
        confirmed: number;
        financialAidAmount  : any;
    }

    export interface IAttender {
        attenderId: any;
        firstName : string;
        lastName : string;
        middleName : string;
        dateOfBirth : any;
        affiliationCode : string;
        otherAffiliation : string;
        firstTimer : number;
        teacher : number;
        guest : number;
        notes : string;
        linens : number;
        arrivalTime  : string;
        departureTime  : string;
        vegetarian : number;
        attended : number;
        singleOccupant : number;
        glutenFree : number;
        changed: boolean;
        housingTypeId : any;
        specialNeedsTypeId : any; // lookup: special needs
        generationId : any; // lookup: generations
        gradeLevel : string; // 'PS','K', 1 .. 13
        ageGroupId : any; // lookup agegroups
        creditTypeId : number; // formerly: feeCredit, lookup: creditTypes
        meals: number[];
    }

    export interface IRegistrationUpdateRequest {
        registration : IRegistrationInfo;
        updatedAttenders : IAttender[];
        deletedAttenders : any[];
        donations: IKeyValuePair[];
    }

    export interface ICostUpdateRequest {
        aidAmount  : any;
        attenders : IAttender[];
        deletedAttenders : any[];
        donations: IKeyValuePair[];
        getFundList: number;
    }

    export interface IAgeGroup extends IListItem {
        cutoffAge : any;
    }

    export interface IHousingTypeListItem extends IListItem {
        category: any;
    }

    export interface IAnnualSessionInfo {
        year : string;
        startDate : any;
        endDate : any;
        datesText : string;
        location : string
    }

    export interface IRegistrationInitResponse {
        sessionInfo : IAnnualSessionInfo;
        user : IRegistrationUser;
        registrationId : any;
        selectedRegistration: IRegistrationResponse;
    }


    export interface IAttenderLookups {
        housingTypes: IHousingTypeListItem[];
        affiliationCodes : IListItem[];
        ageGroups : IAgeGroup[];
    }

    export interface IGetAttenderResponse {
        attender: IAttender;
        lookups: IAttenderLookups;
    }

    export interface IGetAttenderRequest {
        id : any;
        includeLookups : number;
    }

    export interface IFamilyAttender extends INameValuePair {
        firstName: string;
        lastName: string;
        middleName: string;
        generation: number;
        dateOfBirth: string;
    }

    export interface IFindRegistrationAddressResponse {
        name: string;
        address: string;
        city: string;
        persons: IFamilyAttender[];
    }

    export interface IPaymentItem {
        paymentId : any;
        dateReceived : any; // date
        amount: number; // currency
        checkNumber : string; // number or 'cash'
        payor : string;
    }

    export interface IAccountSummary {
        funds: ILookupItem[];
        fees : IListItem[];
        credits: IListItem[];
        donations: IIndexedItem[];
        payments: IPaymentItem[];
        feeTotal: string;
        creditTotal: string;
        donationTotal: string;
        aidEligibility: string;
        balance: any;
    }

    export interface IRegistrationResponse {
        registration: IRegistrationInfo;
        accountSummary: IAccountSummary;
        attenderList: IListItem[];
        housingAssignments: IListItem[];
    }

    export interface IHousingUnit {
        housingUnitId: any;
        unitname: string;
        description: string;
        capacity: number;
        occupants: number;
        housingTypeCode: string;
        housingTypeName: string;
        active: any;
    }
    export interface IHousingType {
        housingTypeId: any;
        housingTypeCode: string;
        housingTypeDescription: string;
        category: number;
        active: any;
    }
    export interface IHousingAssignment {
        housingAssignmentId : any;
        day: number;
        dayName: string;
        housingUnitId: number;
        note: string;
        confirmed: boolean;
    }

    export interface IAttenderHousingAssignment {
        attenderId : number;
        attenderName: string;
        assignments: IHousingAssignment[];
    }


    export interface IGetHousingAssignmentsResponse {
        registrationId: number;
        registrationName: string;
        arrivalDay: number;
        departureDay: number;
        assignments: IAttenderHousingAssignment[];
        units: IHousingUnit[];
    }

    export interface IGetHousingAssignmentsRequest {
        registrationId: number;
        getUnits: boolean;
    }



}
