///<reference path="../Tops.App/registration.d.ts"/>
/**
 * Created by Terry on 12/18/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
///<reference path="../Tops.Peanut/Peanut.ts"/>
module Tops {
    import IHousingUnit = Tops.IHousingUnit;
    export class housingAssignmentComponent {
        private application : IPeanutClient;
        private peanut : Peanut;


        public housingUnitsList = ko.observableArray<IHousingUnit>();
        public registrationName = ko.observable('');


        public constructor(application : IPeanutClient) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
        }

        public getAssignments(registrationId : number) {
            var me = this;

            me.application.hideServiceMessages();
            me.application.showWaiter('Getting assignments...');

            // fake *****************

                var fakeAssignments : IAttenderHousingAssignment[] = [
                    {
                        attenderId: 1,
                        attenderName: 'Terry',
                        assignments: [
                            {
                                housingAssignmentId: 1,
                                day: 5,
                                dayName: '',
                                housingUnitId: 1,
                                note: '',
                                confirmed: false
                            },
                            {
                                housingAssignmentId: 1,
                                day: 6,
                                dayName: '',
                                housingUnitId: 1,
                                note: '',
                                confirmed: false
                            }
                        ]
                    }
                ] ;

                var fakeUnits : IHousingUnit[] = [
                    {
                        housingUnitId: 1,
                        unitname: 'Cabin A1',
                        description: '',
                        capacity: 4,
                        occupants: 2,
                        housingTypeCode: 'OWLW',
                        housingTypeName: 'Night Owl Women',
                        active: true
                    },
                    {
                        housingUnitId: 2,
                        unitname: 'Cabin J1',
                        description: '',
                        capacity: 6,
                        occupants: 0,
                        housingTypeCode: 'FAMILY',
                        housingTypeName: 'Family Cabin',
                        active: true
                    },
                    {
                        housingUnitId: 3,
                        unitname: 'Motel 6',
                        description: '',
                        capacity: 2,
                        occupants: 0,
                        housingTypeCode: 'MOTEL',
                        housingTypeName: 'Camp Motel',
                        active: true
                    }
                ];

                var fakeResponse : IGetHousingAssignmentsResponse = {
                    registrationId: 1,
                    registrationName: 'Terry and Liz',
                    arrivalDay: 4,
                    departureDay: 7,
                    assignments: fakeAssignments,
                    units: fakeUnits
                };

            var response = new fakeServiceResponse(fakeResponse);
            me.handleGetHousingAssignments(response);
            me.application.hideWaiter();

            /*
            me.peanut.executeService('registration.GetHousingAssignments',registrationId, me.handleGetHousingAssignments)
                .always(function() {
                    me.application.hideWaiter();
                });
            */
        }

        private handleGetHousingAssignments = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var response = <IGetHousingAssignmentsResponse>serviceResponse.Value;
                for (var i = response.arrivalDay; i < response.departureDay; i++) {

                }

            }
        };


        private dayToString(day: number) {
            switch (day) {
                case 4 :
                    return 'Thursday';
                case 5 :
                    return 'Friday';
                case 6 :
                    return 'Saturday';
                case 7 :
                    return 'Sunday';
            }
            return 'Error, invalid day ' + day;
        }

    }
}
