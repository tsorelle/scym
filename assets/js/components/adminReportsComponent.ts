/**
 * Created by Terry on 1/4/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class adminReportsComponent  {

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        selectedReport = ko.observable('landing');

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
        }

        showReptMealCountsRequested		= () => {var me=this; me.selectedReport('mealCountsRequested');};
        showReptMealCountsConfirmed     = () => {var me=this; me.selectedReport('mealCountsConfirmed');};
        showReptMealRoster              = () => {var me=this; me.selectedReport('mealRoster');};
        showReptRegistrationsReceived   = () => {var me=this; me.selectedReport('registrationsReceived');};
        showReptRegisteredAttenders     = () => {var me=this; me.selectedReport('registeredAttenders');};
        showReptAttendersByMeeting      = () => {var me=this; me.selectedReport('attendersByMeeting');};
        showReptAttendersByArrival      = () => {var me=this; me.selectedReport('attendersByArrival');};
        showReptNotCheckedIn            = () => {var me=this; me.selectedReport('notCheckedIn');};
        showReptDropIns                 = () => {var me=this; me.selectedReport('dropIns');};
        showReptIncompleteRegistrations = () => {var me=this; me.selectedReport('incompleteRegistrations');};
        showReptPaymentsReceived        = () => {var me=this; me.selectedReport('paymentsReceived');};
        showReptMiscCounts              = () => {var me=this; me.selectedReport('miscCounts');};
        showReptFinancialAid            = () => {var me=this; me.selectedReport('financialAid');};
        showReptLedger                  = () => {var me=this; me.selectedReport('ledger');};

        refreshReports = () => {
            var me = this;
        }
    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.adminReportsComponent);
