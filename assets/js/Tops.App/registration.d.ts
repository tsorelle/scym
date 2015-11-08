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
        contactRequested : number;
        arrivalTime : string;
        departureTime : string;
        scymNotes : string;
        statusDate : any;
        YMDonation : any;
        simpleMealDonation : any;
        financialAidRequested  : any;
        financialAidContribution : any;
        attended : number;
        financialAidAmount : any;
        confirmed: number;
        changed: boolean;
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
    }
    export interface IRegistration {
        info : IRegistrationInfo;
        attendees : IAttender[];
    }

    export interface IAgeGroup {
        ageGroupId : any;
        groupName : string;
        cutoffAge : any;
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
        user : IUser;
        registrationId : any;
    }

    export interface IGetRegistrationRequest {
        lookupCode: any,
        getLookupTables: number;
    }

    export interface IGetRegistrationResponse {
        registration : IRegistration;
        statusTypes : IListItem[];
        specialNeedsTypes: IListItem[];
        generationTypes : IListItem[];
        creditTypes : IListItem[];
        ageGroups : IAgeGroup[];
    }
}
