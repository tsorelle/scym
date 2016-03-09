/**
 * Created by Terry on 1/15/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../Tops.App/Registration.d.ts' />

module Tops {
    export class ageGroupsComponent  {

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;

        ageGroups = ko.observableArray<IAgeGroupEditItem>();
        ageGroupForm = {
            hasErrors : ko.observable(false),
            nameError : ko.observable(''),
            cutoffError : ko.observable(''),
            errorMessage : ko.observable(''),
            name: ko.observable(''),
            cutoffAge: ko.observable('')
        };

        cutoffMonth = ko.observable<INameValuePair>();

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.getAgeGroups();
            if (finalFunction) {
                finalFunction();
            }
        }

        showNewAgeGroupDialog = () => {
            var me = this;
            me.ageGroupForm.cutoffAge('');
            me.ageGroupForm.errorMessage('');
            me.ageGroupForm.name('');
            me.ageGroupForm.hasErrors(false);
            me.ageGroupForm.nameError('');
            me.ageGroupForm.cutoffError('');

            jQuery("#new-agegroup-modal").modal('show');
        };

        getAgeGroups() {
            var me = this;
            me.ageGroups([]);
            var request = "all";
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting age groups...');
            me.peanut.executeService('registration.GetAgeGroupList',request, me.handleGetAgeGroups).always(function() {
                    me.application.hideWaiter();
                });
        }


        saveChanges = () => {
            this.doUpdate();
        };

        private doUpdate(cutoffMonth = 0) {
            var me = this;
            me.application.hideServiceMessages();
            var me = this;
            var updated = [];
            var list = me.ageGroups();
            var valid = true;
            _.each(list,function(item: IAgeGroupEditItem) {
                var validation = Peanut.validatePositiveWholeNumber(item.cutoffAge,18);
                if (validation.errorMessage) {
                    valid = false;
                    item.errorMessage = validation.errorMessage;
                }
                else {
                    item.errorMessage = '';

                    var active = item.active ? true : false;

                    if (validation.text != item.priorState.age || active != item.priorState.active) {
                        item.Description = '(updated: save changes)';
                        updated.push(
                            {
                                id : item.Value,
                                cutoff: item.cutoffAge,
                                active: item.active,
                                name: item.Text
                            }
                        );
                    }
                    item.cutoffAge = validation.text;
                }
            });

            if (valid) {
                if (updated.length == 0 && cutoffMonth == 0) {
                    me.application.showWarning('No changes to save.');
                }
                else {
                    var request = {
                        updates: updated,
                        updateAssignments: cutoffMonth
                    };

                    me.application.hideServiceMessages();
                    me.application.showWaiter('Updating...');


                    me.peanut.executeService('registration.UpdateAgeGroups', request,
                            function(serviceResponse: IServiceResponse) {
                                if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                                    if (cutoffMonth) {
                                        me.owner.handleEvent('youthlistchanged');
                                    }
                                    else {
                                        me.owner.handleEvent('agegroupschanged');
                                    }
                                    me.handleGetAgeGroups(serviceResponse);
                                }
                            }
                        )
                        .always(function () {
                            me.application.hideWaiter();
                        });
                }
            }
            else {
                me.setAgeGroupList(list);
            }
        };

        addAgeGroup = () => {
            var me = this;
            me.ageGroupForm.hasErrors(false);
            me.ageGroupForm.nameError('');
            me.ageGroupForm.cutoffError('');
            var nameText = me.ageGroupForm.name().trim();
            if (nameText == '') {
                me.ageGroupForm.hasErrors(true);
                me.ageGroupForm.nameError(': Please enter a group name.');
            }
            var cutoff = Peanut.validatePositiveWholeNumber(me.ageGroupForm.cutoffAge(),18);
            if (cutoff.errorMessage) {
                me.ageGroupForm.hasErrors(true);
                me.ageGroupForm.cutoffError(':' + cutoff.errorMessage);
            }

            if (me.ageGroupForm.hasErrors()) {
                return;
            }

            var newGroup : IAgeGroupEditItem = {
                active: true,
                cutoffAge: cutoff.value,
                priorState: {
                    age: -1,
                    active: false
                },
                Description: '(new: save changes)',
                errorMessage: '',
                Text: nameText,
                Value: 0
            };

            var list = me.ageGroups();
            list.push(newGroup);
            me.setAgeGroupList(list); //  ageGroups( _.sortBy(list,'cutoffAge'));

            jQuery("#new-agegroup-modal").modal('hide');
        };

        private setAgeGroupList(list: IAgeGroupEditItem[]) {
            var me = this;
            me.ageGroups([]);
            me.ageGroups( _.sortBy(list,function(item: IAgeGroupEditItem) {
                var value = item.cutoffAge;
                if (!value)  {
                    return 0;
                }
                return parseInt(value);
            }));
        }

        showUpdateAssignmentsDialog = () => {
            jQuery("#reassign-agegroup-modal").modal('show');
        };
        updateAssignments = () => {
            var me = this;
            jQuery("#reassign-agegroup-modal").modal('hide');
            this.doUpdate(me.cutoffMonth().Value);
        };

        removeGroup = (item: IAgeGroupEditItem) => {
            var me  = this;
            me.ageGroups.remove(item);

        };

        private handleGetAgeGroups = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var list = <IAgeGroupEditItem[]>serviceResponse.Value;
                _.each(list,function(item: IAgeGroupEditItem){
                    item.errorMessage = '';
                    item.priorState = {
                        age: item.cutoffAge,
                        active: item.active ? true : false
                    };

                });
                me.ageGroups([]);
                me.ageGroups(list);
            }
        };

    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.ageGroupsComponent);
