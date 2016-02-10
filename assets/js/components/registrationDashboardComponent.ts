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
/// <reference path="paymentFormComponent.ts"/>


module Tops {



    export class registrationDashboardComponent implements IRegistrationComponent {

        // private testing = true; // todo: set this false for production
        private testing = false;

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IRegistrationHost;
        private isRefreshLoad = false;

        registrationId = ko.observable();

        registration = {
            name: ko.observable(''),
            address: ko.observable(''),
            city: ko.observable(''),
            phone: ko.observable(''),
            email: ko.observable(''),
            notes: ko.observable(''),
            registrationCode: ko.observable(''),
            status: ko.observable(0),
            statusText: ko.observable(''),
            balanceDue: ko.observable(0.00),
            balance: ko.observable(''),
            housingAssignment: ko.observable(''),
        };

        attenderForm = {
            arrived: ko.observable(''),
            name: ko.observable(''),
            ageGroup: ko.observable(''),
            dietPreference: ko.observable(''),
            specialNeeds: ko.observable(''),
            firstTimer: ko.observable(''),
            meeting: ko.observable(''),
            note: ko.observable(''),
            linens: ko.observable(''),
            housingAssignments: ko.observableArray<IHousingInfoItem>([])
        };

        attenders = ko.observableArray();
        private unCheckedCount = 0;

        dashboardCloseMessage = ko.observable('Closing?');

        changed = ko.observable(false);

        checkinEnabled = ko.observable(false);

        paymentForm : IDataEntryForm;

        public constructor(application:IPeanutClient, owner: IRegistrationHost = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.application.loadResources('paymentFormComponent.js',function() {
                me.paymentForm = new paymentFormComponent();
                me.application.registerComponent('payment-form',me.paymentForm,finalFunction);
            });
        }



        public refresh() {
            var me = this;
            me.isRefreshLoad = true;
            me.getRegistration(me.registrationId());
        }

        public getRegistrationHousing(registrationId) {
            var me = this;
            me.getRegistration(registrationId,'registrationhousingloaded');
        }


        public getRegistration(registrationId: any, nextEvent: string = 'registrationdashboardloaded') {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting the registration...');

            me.peanut.executeService('registration.GetRegistrationDashboard',registrationId,
                function (serviceResponse:IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        me.loadRegistrationResponse(<IRegistrationDashboardResponse>serviceResponse.Value,nextEvent);
                    }
                }).always(function() {
                 me.application.hideWaiter();
             });
        }

        closeDashboard = () => {
            var me = this;
            me.owner.getRegistrationContext(
                function(context: IRegistrationContext) {
                    var errorMessage = '';
                    var today = me.testing ? new Date('2016-3-29') : new Date();
                    var ymStart = new Date(context.sessionInfo.startDate);

                    if (today >= ymStart ) {
                        me.checkinEnabled(true);

                        if (me.unCheckedCount > 0) {
                            errorMessage = 'Some attenders not checked in';
                        }

                        if (me.registration.balanceDue()) {
                            if (errorMessage) {
                                errorMessage += ' and balance not paid. '
                            }
                            else {
                                errorMessage = 'Balance not paid. '
                            }
                        }
                        else if (errorMessage) {
                            errorMessage += '.';
                        }

                        if (errorMessage) {
                            errorMessage += ' Leave anyway?'
                        }
                    }
                    else {
                        me.checkinEnabled(false);
                    }

                    if (errorMessage) {
                        me.dashboardCloseMessage(errorMessage);
                        jQuery("#confirm-dashboard-close").modal('show');
                    }
                    else {
                        me.owner.handleEvent('dashboardclosed');
                    }
                }
            );
        };


        exitDashboard = () => {
            var me = this;
            jQuery("#confirm-dashboard-close").modal('hide');
            me.owner.handleEvent('dashboardclosed');
        };

        private loadRegistrationResponse(response : IRegistrationDashboardResponse, nextEvent: string) {
            var me = this;
            var isRefresh = me.isRefreshLoad;
            me.isRefreshLoad = false;
            me.owner.getRegistrationContext(
                function(context: IRegistrationContext) {
                    var today = me.testing ? today = new Date('2016-3-29') : new Date();
                    var ymStart = new Date(context.sessionInfo.startDate);
                    me.checkinEnabled(today >= ymStart );
                    me.paymentForm.clear();
                    me.registrationId(response.registrationId);
                    me.registration.name(response.name);
                    me.registration.address(response.address);
                    me.registration.city(response.city);
                    me.registration.phone(response.phone);
                    me.registration.email(response.email);
                    me.registration.notes(response.notes);
                    me.registration.registrationCode(response.registrationCode);
                    me.registration.status(response.status);
                    me.registration.statusText(response.statusText);
                    me.registration.balanceDue(response.balanceDue);
                    me.registration.housingAssignment(response.housingAssignment);

                    if (!response.balanceDue) {
                        me.registration.balance('Paid in full');
                    }
                    else {
                        me.registration.balance('$' + response.balanceDue);
                    }

                    me.unCheckedCount = response.attenders.length;
                    _.each(response.attenders, function (attender:IAttenderCheckListItem) {
                        attender.arrived = (attender.arrived == 'Yes' || attender.arrived == true);
                        if (attender.arrived) {
                            me.unCheckedCount = -1;
                        }
                    });

                    me.attenders(response.attenders);
                    me.changed(false);
                    if (!isRefresh) {
                        me.owner.handleEvent(nextEvent, response.registrationId);
                    }
                }
            );
        }

        showAttenderDetails = (attender : IAttenderCheckListItem) => {
            var me = this;
            me.attenderForm.arrived(attender.arrived ? 'Yes' : 'No');
            me.attenderForm.name(attender.name);
            me.attenderForm.ageGroup(attender.ageGroup);
            me.attenderForm.dietPreference(attender.dietPreference);
            me.attenderForm.specialNeeds(attender.specialNeeds);
            me.attenderForm.firstTimer(attender.firstTimer);
            me.attenderForm.meeting(attender.meeting);
            me.attenderForm.note(attender.note);
            me.attenderForm.linens(attender.linens);
            me.attenderForm.housingAssignments(attender.housingAssignments);
            jQuery("#attender-detail-modal").modal('show');
        };

        checkIn = () => {
            var me = this;
            var errorMessage = '';
            var attenders = me.attenders();
            var unchecked = attenders.length;
            _.each(attenders, function (attender: IAttenderCheckListItem) {
                if (attender.arrived) {
                    unchecked -= 1;
                }
            });

            if (unchecked) {
                errorMessage = 'Some attenders are not checked';
            }

            var due = me.registration.balanceDue();
            if (due) {
                var validPayment = me.paymentForm.validate();
                if (!validPayment) {
                    errorMessage = 'Please correct the payment form or enter blank amount before saving';
                    me.dashboardCloseMessage(errorMessage);
                    // me.application.showError('Please correct the payment form or enter blank amount before saving');
                    jQuery("#dashboard-alert").modal('show');
                    return;
                }
                else {
                    var payment = me.paymentForm.getValues();
                    if (payment.amount < due) {
                        if (unchecked) {
                            errorMessage += ' and a balance is still outstanding'
                        }
                        else {
                            errorMessage = 'A balance is still outstanding'
                        }
                    }
                }
            }

            if (errorMessage) {
                errorMessage += '. Still want to save?';
                me.dashboardCloseMessage(errorMessage);
                jQuery("#confirm-dashboard-save").modal('show');
            }
            else {
                me.saveAndExit(false);
            }
        };

        saveAndExit = (closeModal = true) => {
            var me = this;
            if (closeModal) {
                jQuery("#confirm-dashboard-save").modal('hide');
            }

            var paymentValues = me.paymentForm.getValues();
            if (paymentValues != null && paymentValues.amount < 1) {
                paymentValues = null;
            }

            var request = {
                registrationId : me.registrationId(),
                payment : paymentValues,
                attenders : []
            };

            _.each(me.attenders(),function(attender: IAttenderCheckListItem){
                request.attenders.push(
                    {
                        attenderId: attender.attenderId,
                        arrived: attender.arrived
                    }
                );
            });

            me.application.hideServiceMessages();
            me.application.showWaiter('Checking in...');

             me.peanut.executeService('registration.CheckIn',request, me.handleCheckInResponse)
                 .always(function() {
                     me.application.hideWaiter();
                 });

        };

        private handleCheckInResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.changed(false);
                me.owner.handleEvent('dashboardclosed');
            }
        };

        toggleCheckin = () => {
            var me = this;
            me.changed(true);
            return true; // required to keep click event from re-checking the checkbox
        };


    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.registrationDashboardComponent);
