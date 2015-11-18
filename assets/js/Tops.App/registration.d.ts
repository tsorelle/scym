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
        amountPaid : any;
        notes : string;
        feesReceivedDate : any;
        arrivalTime : string;
        departureTime : string;
        scymNotes : string;
        statusDate : any;
        confirmed: number;

        ymDonation : any;
        simpleMealDonation : any;
        aidRequested  : any;
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
        financialAidRequested : number;
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
    }

    export interface ICostUpdateRequest {
        ymDonation : any;
        simpleMealDonation : any;
        aidRequested  : any;
        attenders : IAttender[];
        deletedAttenders : any[];
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

    export interface IAccountSummary {
        fees : IListItem[];
        credits: IListItem[];
        donations: IListItem[];
        feeTotal: string;
        creditTotal: string;
        donationTotal: string;
        balance: any;
    }

    export interface IRegistrationResponse {
        registration: IRegistrationInfo;
        accountSummary: IAccountSummary;
        attenderList: IListItem[];
        housingPreference: string;
        housingAssignments: IListItem[];
    }

}
