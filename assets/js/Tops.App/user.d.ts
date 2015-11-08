/**
 * Created by Terry on 6/5/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
declare module Tops {
    export interface IUser {
        id:string;
        name:string;
        authenticated : number;
        authorized: number;
        email:string;
    }
}
