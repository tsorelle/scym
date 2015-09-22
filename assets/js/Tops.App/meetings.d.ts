/// <reference path='../Tops.Peanut/Peanut.d.ts' />
declare module Tops {
    export interface IScymMeeting {
        meetingId  : any;
        meetingName: string;
        state :  string;
        area: string;
        address :  string;
        affiliationCode :  string;
        worshipTimes:  string;
        worshipLocation:  string;
        url:  string;
        detailText:  string;
        note:  string;
        latitude : any;
        longitude :any;
        lastUpdate:  string;
        active : number;
        updatedBy:  string;
        quarterlyMeetingId : any;
        quarterlyMeetingName:  string;

        mailFormLink:  string;
        email:  string;
        editState : number;
    }

    interface IInitMeetingsResponse {
        meetings: IScymMeeting[];
        quarterlies:  IListItem[];
        canEdit: boolean;
    }



}